<?php

namespace App\Http\Controllers;

use App\Models\Diagnose;
use App\Models\Symptom;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class SymptomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $items = Symptom::roots()->get();
        return view('symptom.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $item = Symptom::find($id);
        $hierarchy = $item->ancestors()->get();

        if($item->isLeaf())
        {
            $diagnoses = $item->diagnoses;
            $availableDiagnoses = Diagnose::lists('name', 'id');

            return view('symptom.show_diagnose', compact('item', 'diagnoses', 'hierarchy', 'availableDiagnoses'));
        }

        $children = $item->children()->get();
        return view('symptom.show', compact('item', 'children', 'hierarchy'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    public function addDiagnose($id)
    {
        $symptom = Symptom::find($id);
        $symptom->diagnoses()->attach(Input::get('diagnose_id'));

        return redirect()->to('symptom/' . $id);
    }
}
