<?php

namespace App\Http\Controllers;

use App\Models\Diagnose;
use App\Models\Symptom;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class SymptomController extends Controller
{
    function __construct()
    {
        $assessment = Session::get('assessment', []);
        $appliedDiagnoses = $rejectedDiagnoses = [];

        foreach($assessment as $ass)
        {
            if($ass['action'] == 'reject')
            {
                $rejectedDiagnoses[] = $ass['diagnose_id'];
            }
            else
            {
                $appliedDiagnoses[] = $ass['diagnose_id'];
            }
        }
        view()->share('appliedDiagnoses', $appliedDiagnoses);
        view()->share('rejectedDiagnoses', $rejectedDiagnoses);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $items = Symptom::roots()->whereLanguage(config('app.locale'))->get();
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
    public function show(Request $request, $id)
    {
        $item = Symptom::find($id);
        $hierarchy = $item->ancestors()->get();

        if($item->isLeaf())
        {
            $diagnoses = $item->diagnoses;
            $availableDiagnoses = Diagnose::lists('name', 'id');

            $assessment = $request->session()->get('assessment', []);
            $appliedDiagnoses = $rejectedDiagnoses = [];
            foreach($assessment as $ass)
            {
                if($ass['action'] == 'reject')
                {
                    $rejectedDiagnoses[] = $ass['diagnose_id'];
                }
                else
                {
                    $appliedDiagnoses[] = $ass['diagnose_id'];
                }
            }

            return view('symptom.show_diagnose', compact('item', 'diagnoses', 'hierarchy', 'availableDiagnoses', 'assessment', 'appliedDiagnoses', 'rejectedDiagnoses'));
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

    public function assessment(Request $request)
    {
        $request->session()->push('assessment', $request->except('_token'));
        return redirect()->to('symptom/' . $request->get('symptom_id') . '#diagnose-' . $request->get('diagnose_id'));
    }

    public function printout(Request $request)
    {
        $assessment = $request->session()->get('assessment', []);

        return view('symptom.printout', compact('assessment'));
    }

    public function refresh(Request $request)
    {
        $request->session()->remove('assessment');
        return redirect()->to('/');

    }

}
