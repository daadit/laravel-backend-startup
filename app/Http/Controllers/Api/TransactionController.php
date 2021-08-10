<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->transactions = new Transaction();
    }

    public function save(Request $request)
    {
        // Validasi input
        $validasi = Validator::make($request->all(), [
            'transactionUserId' => 'required',
            'transactionTotalItem' => 'required',
            'transactionTotalPrice' => 'required',
            'transactionName' => 'required',
            'transactionPhone' => 'required',
        ]);
        // Jika validasi tidak terpenuhi
        if ($validasi->fails()) {
            $val = $validasi->errors()->all();
            return $this->error($val[0]);
        }

        $paymentCode = "INV-PYM-" . now()->format('Ymd') . "-" . rand(100, 999);
        $transactionCode = "INV-PYM-" . now()->format('Ymd') . "-" . rand(100, 999);
        $uniqueCode = rand(100, 999);
        $status = "MENUNGGU";
        $expiredAt = now()->addDay();

        $dataTransaction = array_merge($request->all(), [
            'transactionPaymentCode' => $paymentCode,
            'transactionCode' => $transactionCode,
            'transactionUniqueCode' => $uniqueCode,
            'transactionStatus' => $status,
            'transactionExpiredAt' => $expiredAt
        ]);

        DB::beginTransaction();

        $transaction = Transaction::create($dataTransaction);

        foreach ($request->products as $product) {
            $dataDetail = [
                'detailTransactionId' => $transaction->id,
                'detailProductId' => $product['detailProductId'],
                'detailTotalItem' => $product['detailTotalItem'],
                'detailTotalPrice' => $product['detailTotalPrice'],
                'detailNote' => $product['detailNote']
            ];

            $transactionDetail = TransactionDetail::create($dataDetail);;
        }

        if (!empty($transaction) && !empty($transactionDetail)) {
            DB::commit();
            return response()->json([
                'success' => 1,
                'message' => 'Transaksi berhasil',
                'transaksi' => collect($transaction)
            ]);
        } else {
            DB::rollback();
            return $this->error('Transaksi gagal');
        }
    }

    public function history($id)
    {
        $transaction = Transaction::with(['user'])->whereHas('user', function ($query) use ($id) {
            $query->whereId($id);
        })->get();

        foreach ($transaction as $transaksi) {
            $details = $transaksi->details;
            foreach ($details as $detail) {
                $detail->product;
            }
        }

        if (!empty($transaction)) {
            return response()->json([
                'success' => 1,
                'message' => 'Data berhasil ditemukan',
                'transaksi' => collect($transaction)
            ]);
        } else {
            return $this->error('Data gagal ditemukan');
        }
    }

    public function error($pesan)
    {
        return response()->json([
            'success' => 0,
            'message' => $pesan
        ]);
    }
}
