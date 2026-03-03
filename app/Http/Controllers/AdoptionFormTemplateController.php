<?php

namespace App\Http\Controllers;

use App\Contracts\AdoptionFormTemplateServiceInterface;
use App\Models\AdoptionFormTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdoptionFormTemplateController extends Controller
{
    protected AdoptionFormTemplateServiceInterface $service;

    public function __construct(AdoptionFormTemplateServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * List all templates for the authenticated user.
     */
    public function index()
    {
        $templates = $this->service->getByUser(Auth::id());

        return response()->json([
            'success' => true,
            'data' => $templates,
        ]);
    }

    /**
     * Create a new form template.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'schema' => 'required|array|min:1',
            'schema.*.label' => 'required|string|max:255',
            'schema.*.type' => 'required|string|in:' . implode(',', AdoptionFormTemplate::supportedFieldTypes()),
            'schema.*.required' => 'boolean',
            'schema.*.options' => 'nullable|array',
            'is_default' => 'boolean',
        ]);

        $template = $this->service->create(Auth::id(), $validated);

        return response()->json([
            'success' => true,
            'message' => __('Template created successfully'),
            'data' => $template,
        ], 201);
    }

    /**
     * Show a single template.
     */
    public function show($id)
    {
        $template = $this->service->findOrFail($id, Auth::id());

        return response()->json([
            'success' => true,
            'data' => $template,
        ]);
    }

    /**
     * Update an existing template.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'schema' => 'sometimes|required|array|min:1',
            'schema.*.label' => 'required_with:schema|string|max:255',
            'schema.*.type' => 'required_with:schema|string|in:' . implode(',', AdoptionFormTemplate::supportedFieldTypes()),
            'schema.*.required' => 'boolean',
            'schema.*.options' => 'nullable|array',
            'is_default' => 'boolean',
        ]);

        $template = $this->service->update($id, Auth::id(), $validated);

        return response()->json([
            'success' => true,
            'message' => __('Template updated successfully'),
            'data' => $template,
        ]);
    }

    /**
     * Delete a template.
     */
    public function destroy($id)
    {
        $this->service->delete($id, Auth::id());

        return response()->json([
            'success' => true,
            'message' => __('Template deleted successfully'),
        ]);
    }
}
