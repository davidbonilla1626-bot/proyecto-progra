<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    /**
     * Aplica un código promocional al subtotal enviado.
     */
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $code = strtoupper($request->code);
        $subtotal = (float)$request->subtotal;

        $promotion = Promotion::where('code', $code)->first();

        if (!$promotion) {
            return response()->json([
                'valid' => false,
                'message' => 'El cupón ingresado no existe.'
            ]);
        }

        if (!$promotion->isValid()) {
            return response()->json([
                'valid' => false,
                'message' => 'Este cupón ha vencido.'
            ]);
        }

        $discount = $promotion->calculateDiscount($subtotal);

        return response()->json([
            'valid' => true,
            'code' => $promotion->code,
            'type' => $promotion->type,
            'value' => $promotion->value,
            'discount' => $discount,
            'message' => '¡Cupón aplicado correctamente!'
        ]);
    }
}
