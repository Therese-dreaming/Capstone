<?php

namespace App\Http\Controllers;

use App\Models\Laboratory;
use Illuminate\Http\Request;

class LaboratoryController extends Controller
{
	public function index()
	{
		$labs = Laboratory::orderBy('number')->paginate(15);
		return view('laboratories.index', compact('labs'));
	}

	public function create()
	{
		return view('laboratories.create');
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'number' => 'required|string|max:50|unique:laboratories,number',
			'name' => 'nullable|string|max:255',
			'building' => 'nullable|string|max:255',
			'floor' => 'nullable|string|max:50',
			'room_number' => 'nullable|string|max:50',
		]);

		Laboratory::create($validated);

		return redirect()->route('laboratories.index')->with('success', 'Laboratory created successfully');
	}

	public function edit(Laboratory $laboratory)
	{
		return view('laboratories.edit', compact('laboratory'));
	}

	public function update(Request $request, Laboratory $laboratory)
	{
		$validated = $request->validate([
			'number' => 'required|string|max:50|unique:laboratories,number,' . $laboratory->id,
			'name' => 'nullable|string|max:255',
			'building' => 'nullable|string|max:255',
			'floor' => 'nullable|string|max:50',
			'room_number' => 'nullable|string|max:50',
		]);

		$laboratory->update($validated);

		return redirect()->route('laboratories.index')->with('success', 'Laboratory updated successfully');
	}

	public function destroy(Laboratory $laboratory)
	{
		$laboratory->delete();
		return redirect()->route('laboratories.index')->with('success', 'Laboratory deleted successfully');
	}
} 