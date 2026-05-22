<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'tenant', 'subscription']);
    }

    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $type = $request->query('type');

        // Get tenant templates
        $query = DocumentTemplate::where('tenant_id', $tenantId)
            ->orderBy('is_default', 'desc')
            ->orderBy('type')
            ->orderBy('name');

        if ($type && in_array($type, ['invoice', 'quotation', 'receipt', 'delivery_note'])) {
            $query->where('type', $type);
        }

        $templates = $query->get();

        // Get system templates
        $systemQuery = DocumentTemplate::system();

        if ($type && in_array($type, ['invoice', 'quotation', 'receipt', 'delivery_note'])) {
            $systemQuery->where('type', $type);
        }

        $systemTemplates = $systemQuery->get();

        return view('settings.templates.index', compact('templates', 'systemTemplates', 'type'));
    }

    public function use($id)
    {
        $tenantId = auth()->user()->tenant_id;
        $template = DocumentTemplate::findOrFail($id);

        if ($template->is_system) {
            // Clone system template to tenant
            $newTemplate = DocumentTemplate::create([
                'tenant_id' => $tenantId,
                'name' => $template->name,
                'type' => $template->type,
                'html_content' => $template->html_content,
                'thumbnail' => $template->thumbnail,
                'settings' => $template->settings,
                'is_default' => true,
                'is_system' => false,
                'is_locked' => true, // Lock the copy so it cannot be edited
            ]);

            // Unset other defaults of the same type
            DocumentTemplate::where('tenant_id', $tenantId)
                ->where('type', $template->type)
                ->where('id', '!=', $newTemplate->id)
                ->update(['is_default' => false]);

            return back()->with('success', 'Template berhasil ditambahkan dan diaktifkan');
        } else {
            // Check ownership
            if ($template->tenant_id != $tenantId) {
                abort(403);
            }

            // Set as default
            $template->update(['is_default' => true]);

            // Unset other defaults
            DocumentTemplate::where('tenant_id', $tenantId)
                ->where('type', $template->type)
                ->where('id', '!=', $template->id)
                ->update(['is_default' => false]);

            return back()->with('success', 'Template berhasil diaktifkan sebagai default');
        }
    }

    public function create()
    {
        return view('settings.templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:invoice,quotation,receipt,delivery_note',
            'html_content' => 'required|string',
            'is_default' => 'boolean',
        ]);

        $tenantId = auth()->user()->tenant_id;

        DB::transaction(function () use ($validated, $tenantId) {
            // If setting as default, unset other defaults of same type
            if ($validated['is_default'] ?? false) {
                DocumentTemplate::where('tenant_id', $tenantId)
                    ->where('type', $validated['type'])
                    ->update(['is_default' => false]);
            }

            DocumentTemplate::create([
                'tenant_id' => $tenantId,
                'name' => $validated['name'],
                'type' => $validated['type'],
                'html_content' => $validated['html_content'],
                'thumbnail' => 'https://placehold.co/400x300/e2e8f0/475569?text=' . urlencode($validated['name']), // Default thumbnail
                'is_default' => $validated['is_default'] ?? false,
                'is_system' => false,
            ]);
        });

        return redirect()->route('settings.templates.index')
            ->with('success', 'Template berhasil dibuat');
    }

    public function edit(DocumentTemplate $template)
    {
        $this->authorize('update', $template);
        return view('settings.templates.edit', compact('template'));
    }

    public function update(Request $request, DocumentTemplate $template)
    {
        $this->authorize('update', $template);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'html_content' => 'required|string',
            'is_default' => 'boolean',
        ]);

        $tenantId = auth()->user()->tenant_id;

        DB::transaction(function () use ($validated, $tenantId, $template) {
            // If setting as default, unset other defaults of same type
            if ($validated['is_default'] ?? false) {
                DocumentTemplate::where('tenant_id', $tenantId)
                    ->where('type', $template->type)
                    ->where('id', '!=', $template->id)
                    ->update(['is_default' => false]);
            }

            $template->update([
                'name' => $validated['name'],
                'html_content' => $validated['html_content'],
                'is_default' => $validated['is_default'] ?? false,
            ]);
        });

        return back()->with('success', 'Template berhasil diperbarui');
    }

    public function destroy(DocumentTemplate $template)
    {
        $this->authorize('delete', $template);

        if ($template->is_system) {
            return back()->with('error', 'Template sistem tidak dapat dihapus');
        }

        $template->delete();

        return back()->with('success', 'Template berhasil dihapus');
    }

    public function preview($id)
    {
        // Allow previewing system templates too
        $template = DocumentTemplate::findOrFail($id);

        if (!$template->is_system && $template->tenant_id != auth()->user()->tenant_id) {
            abort(403);
        }

        return view('settings.templates.preview', compact('template'));
    }
}
