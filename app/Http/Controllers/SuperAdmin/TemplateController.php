<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TemplateController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type');

        $query = DocumentTemplate::system()
            ->orderBy('type')
            ->orderBy('name');

        if ($type && in_array($type, ['invoice', 'quotation', 'receipt', 'delivery_note'])) {
            $query->where('type', $type);
        }

        $templates = $query->paginate(12);

        return view('superadmin.templates.index', compact('templates', 'type'));
    }

    public function create()
    {
        return view('superadmin.templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:invoice,quotation,receipt,delivery_note',
            'html_content' => 'required|string',
            'thumbnail_url' => 'nullable|url',
        ]);

        DocumentTemplate::create([
            'tenant_id' => null, // System template
            'name' => $validated['name'],
            'type' => $validated['type'],
            'html_content' => $validated['html_content'],
            'thumbnail' => $validated['thumbnail_url'] ?? 'https://placehold.co/400x300/e2e8f0/475569?text=' . urlencode($validated['name']),
            'is_default' => false,
            'is_system' => true,
        ]);

        return redirect()->route('superadmin.templates.index')
            ->with('success', 'Template sistem berhasil dibuat');
    }

    public function edit(DocumentTemplate $template)
    {
        if (!$template->is_system) {
            abort(403, 'Hanya template sistem yang dapat diedit di sini.');
        }
        return view('superadmin.templates.edit', compact('template'));
    }

    public function update(Request $request, DocumentTemplate $template)
    {
        if (!$template->is_system) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'html_content' => 'required|string',
            'thumbnail_url' => 'nullable|url',
        ]);

        $template->update([
            'name' => $validated['name'],
            'html_content' => $validated['html_content'],
            'thumbnail' => $validated['thumbnail_url'] ?? $template->thumbnail,
        ]);

        return back()->with('success', 'Template sistem berhasil diperbarui');
    }

    public function destroy(DocumentTemplate $template)
    {
        if (!$template->is_system) {
            abort(403);
        }

        $template->delete();

        return back()->with('success', 'Template sistem berhasil dihapus');
    }

    public function preview($id)
    {
        $template = DocumentTemplate::findOrFail($id);
        return view('settings.templates.preview', compact('template')); // Reuse existing preview
    }
}
