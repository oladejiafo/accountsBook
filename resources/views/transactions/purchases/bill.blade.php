@extends('layouts.app')

@section('title', 'Purchase Invoice No: ' . $bill->id)

@section('content')

<div class="titles" style="color:#575757; font-style: bold;   border-bottom: 1px solid white;">Purchase Invoice No : {{ $bill->id }}</div>

<form method="post">
    @csrf

    <div class="bg">

        <br>

        <div id="printArea" class="bg">

            <table class="outer-box inner-box table-responsive" style="width: 840px; margin-left: auto; margin-right: auto; border-collapse: collapse;">
                <tbody>

                    <tr style="height: 1px;">
                        <td style="border: 1px solid black;"> <p style="text-align: center;">PURCHASE INVOICE</p> </td>
                    </tr>

                    <tr style="text-align: center;">
                        <td style="border: 1px solid black;">
                            @if(isset($companyName))
                                <span style="font-size: 350%;" class="mr-4 text-center">
                          
                                     {{ $companyName }} 
                                </span>
                            @endif  
                            <br>
                            <span style="font-size: 120%; font-weight: bold;">Business Type: {{ $companyBusiness }}</span> <br>
                            <span style="font-weight: bold;">ADDRESS :</span> {{ $companyAddress }}<br>
                            <span style="font-weight: bold;">EMAIL : {{ $companyEmail }}</span> <br><br>
                        </td>
                    </tr>

                    <tr>
                        <td style="border: 1px solid black;">
                            <table class="outer-box table-responsive" style="width: 800px; margin-left: auto; margin-right: auto; border-collapse: collapse;">
                                <tbody>
                                    <tr>
                                        <td class="inner-box" style="text-align: center; font-weight: bold; border: 1px solid black;" colspan="3">GSTIN NO - 123456789CASTR0</td>
                                    </tr>
                                    <tr>
                                        <td class="inner-box" style="width: 50%; font-weight: bold; border: 1px solid black;">&nbsp;NAME OF CONSIGNEE / SELLER</td>
                                        <td class="inner-box" style="width: 25%; font-weight: bold; border: 1px solid black;">&nbsp;INVOICE NO</td>
                                        <td class="inner-box" style="width: 25%; border: 1px solid black;">&nbsp;{{ $bill->id }}</td>
                                    </tr>
                                    <tr>
                                        <td class="inner-box" style="width: 50%; border: 1px solid black;">&nbsp;{{ $bill->supplier->name }}</td>
                                        <td class="inner-box" style="width: 25%; font-weight: bold; border: 1px solid black;">&nbsp;DATE</td>
                                        <td class="inner-box" style="width: 25%; border: 1px solid black;">&nbsp;{{ $bill->created_at->format('Y-m-d') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="inner-box" style="width: 50%; border: 1px solid black;">&nbsp;{{ $bill->address }}</td>
                                        <td class="inner-box" style="width: 25%; font-weight: bold; border: 1px solid black;">&nbsp;EWAY NO</td>
                                        <td class="inner-box" style="width: 25%; border: 1px solid black;">&nbsp;<input type="text" style="border: none;" name="eway" value="{{ $billdetails->eway ?? '' }}"></td>
                                    </tr>
                                    <tr>
                                        <td class="inner-box" style="width: 50%; border: 1px solid black;">&nbsp;{{ $bill->gstin }}</td>
                                        <td class="inner-box" style="width: 25%; font-weight: bold; border: 1px solid black;">&nbsp;VEH NO</td>
                                        <td class="inner-box" style="width: 25%; border: 1px solid black;">&nbsp;<input type="text" style="border: none;" name="veh" value="{{ $billdetails->veh ?? '' }}"></td>
                                    </tr>
                                    <tr>
                                        <td class="inner-box" style="width: 50%; border: 1px solid black;">&nbsp;</td>
                                        <td class="inner-box" style="width: 25%; font-weight: bold; border: 1px solid black;">&nbsp;PO NO &amp; DATE</td>
                                        <td class="inner-box" style="width: 25%; border: 1px solid black;">&nbsp;<input type="text" style="border: none;" name="po" value="{{ $billdetails->po ?? '' }}"></td>
                                    </tr>
                                    <!-- Add more fields here -->
                                </tbody>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="border: 1px solid black;">
                            <table class="outer-box table-responsive" style="width: 800px; margin-left: auto; margin-right: auto; border-collapse: collapse;">
                                <tbody>
                                    <tr>
                                        <td class="inner-box" style="width: 5%; font-weight: bold; text-align: center; border: 1px solid black;">&nbsp;SL</td>
                                        <td class="inner-box" style="width: 30%; font-weight: bold; text-align: center; border: 1px solid black;">GOODS</td>
                                        <td class="inner-box" style="width: 12%; font-weight: bold; text-align: center; border: 1px solid black;">&nbsp;HSN/SAC</td>
                                        <td class="inner-box" style="width: 12%; font-weight: bold; text-align: center; border: 1px solid black;">QTY MTS</td>
                                        <td class="inner-box" style="width: 12%; font-weight: bold; text-align: center; border: 1px solid black;">RATE PMT</td>
                                        <td class="inner-box" style="width: 12%; font-weight: bold; text-align: center; border: 1px solid black;">AMOUNT {{ $defaultCurrency }}</td>
                                        <td class="inner-box" style="width: 5%; font-weight: bold; text-align: center; border: 1px solid black;">PS</td>
                                    </tr>
                                    @foreach ($bill->items as $item)
                                    <tr style="height: auto;">
                                        <td class="inner-box" style="width: 5%; border: 1px solid black;">&nbsp;{{ $loop->iteration }}</td>
                                        <td class="inner-box" style="width: 30%; border: 1px solid black;">&nbsp;{{ $item->stock->name }}</td>
                                        <td class="inner-box" style="width: 12%; border: 1px solid black;"></td>
                                        <td class="inner-box" style="width: 12%; border: 1px solid black;">&nbsp;{{ $item->quantity }}</td>
                                        <td class="inner-box" style="width: 12%; border: 1px solid black;">&nbsp;{{ $item->perprice }}</td>
                                        <td class="inner-box" style="width: 12%; border: 1px solid black;">&nbsp;{{ $item->totalprice }}</td>
                                        <td class="inner-box" style="width: 5%; border: 1px solid black;">&nbsp;0</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black;">
                            <table class="outer-box table-responsive" style="width: 800px; margin-left: auto; margin-right: auto; border-collapse: collapse;">
                                <tbody>
                                    <tr>
                                        <td class="inner-box" style="width: 35%; text-align: center; border: 1px solid black;" rowspan="6">
                                            <p> <span style="font-weight: bold;">BANK DETAILS <br> CodeAstro</span> <br>
                                                WestView Bank <br> AC NO-54A7 6S31 4T85 0RO3 <br> IFSC CODE - ABCD 010 0110 <br> CS BRANCH <br> PH NO - 541-010-0400</p>
                                        </td>
                                        <td class="inner-box" style="width: 30%; font-weight: bold; border: 1px solid black;">&nbsp;CGST @ 2.5%</td>
                                        <td class="inner-box align-middle" style="width: 30%; border: 1px solid black;">&nbsp; <input type="text" style="border: none;" name="cgst" class="align-middle" pattern="[0-9]+\.[0-9]+" value="{{ $billdetails->cgst ?? '' }}"></td>
                                    </tr>
                                    <tr>
                                        <td class="inner-box" style="font-weight: bold; border: 1px solid black;">&nbsp;SGST @ 2.5%</td>
                                        <td class="inner-box align-middle" style="border: 1px solid black;">&nbsp; <input type="text" style="border: none;" name="sgst" class="align-middle" pattern="[0-9]+\.[0-9]+" value="{{ $billdetails->sgst ?? '' }}"></td>
                                    </tr>
                                    <tr>
                                        <td class="inner-box" style="font-weight: bold; border: 1px solid black;">&nbsp;IGST @ 5% </td>
                                        <td class="inner-box align-middle" style="border: 1px solid black;">&nbsp; <input type="text" style="border: none;" name="igst" class="align-middle" pattern="[0-9]+\.[0-9]+" value="{{ $billdetails->igst ?? '' }}"></td>
                                    </tr>
                                    <tr>
                                        <td class="inner-box" style="font-weight: bold; border: 1px solid black;">&nbsp;CESS @ 400/PMT </td>
                                        <td class="inner-box align-middle" style="border: 1px solid black;">&nbsp; <input type="text" style="border: none;" name="cess" class="align-middle" pattern="[0-9]+\.[0-9]+" value="{{ $billdetails->cess ?? '' }}"></td>
                                    </tr>
                                    <tr>
                                        <td class="inner-box" style="font-weight: bold; border: 1px solid black;">&nbsp;TCS @ 1%</td>
                                        <td class="inner-box align-middle" style="border: 1px solid black;">&nbsp; <input type="text" style="border: none;" name="tcs" class="align-middle" pattern="[0-9]+\.[0-9]+" value="{{ $billdetails->tcs ?? '' }}"></td>
                                    </tr>
                                    <tr>
                                        <td class="inner-box" style="font-weight: bold; border: 1px solid black;">&nbsp;TOTAL</td>
                                        <td class="inner-box align-middle" style="border: 1px solid black;">&nbsp; <input type="text" style="border: none;" name="total" class="align-middle" pattern="[0-9]+\.[0-9]+" value="{{ $billdetails->total ?? '' }}"> </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="border: 1px solid black; text-align: right;">
                            <span style="font-weight: bold;">FOR COMPANY <br><br><br><br> Signature</span>
                        </td>
                    </tr>

                    <tr>
                        <td style="border: 1px solid black; text-align: center;">
                            <!-- FINAL TEXT -->
                        </td>
                    </tr>

                </tbody>
            </table>

        </div>

        <!-- <br><br> -->

    </div>

    <br><br>

    <div class="wrapper" style="text-align: center;">
        <button class="btn btn-primary" onclick="printpage('printArea')">Print</button>
        <button class="btn btn-success" type="submit">Save Draft</button>
        <a href="{{ route('purchase.index') }}" class="btn btn-secondary">Go Back</a>
    </div>

</form>

<script>
    function printpage(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>

@endsection
