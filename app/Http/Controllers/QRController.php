<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barcode;
use Illuminate\Support\Facades\Log;

class QRController extends Controller
{
    public function storeResult(Request $request)
    {
        $request->validate([
            'table_number' => 'required|string',
        ]);
        $exists = Barcode::where('table_number', $request->table_number)->exists();
        if ($exists) {
            session(['table_number' => $request->table_number]);
    
            return response()->json(['status' => 'success']);
        }else{
            return response()->json(['status' => 'failed']);
        }
    }

    public function checkCode($code)
    {
        if (preg_match('/^[a-zA-Z]\d{4}$/', $code)) {
            $exists = Barcode::where('table_number', $code)->exists();

            if ($exists) {
                session(['table_number' => $code]);
                
                return redirect()->route('home')->with('message', 'Welcome! Code verified successfully.');

            } else {
                return view('invalid', [
                    'message' => 'Code not found in the database.',
                ]);
            }
        }
        
        return view('invalid', [
            'message' => 'Invalid code format.',
        ]);
    }
}