<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Invoice {{ $invoiceNumber }}</title>

    <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>

    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 14px;
            line-height: 20px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
            height: 100%
        }

        .invoice-box.second {
            max-width: 800px;
            padding: 0px;
            border: 0px solid #eee;
            box-shadow: 0 0 0px rgba(0, 0, 0, 0);
            font-size: 0px;
            line-height: 0px;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: right;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: left;
        }

        .invoice-box table tr.top table {
            border: 1px solid orange;
            border-top: 3px solid orange;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
            padding-top: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 10px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: 500;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .invoice-box.rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .invoice-box.rtl table {
            text-align: right;
        }

        .invoice-box.rtl table tr td:nth-child(2) {
            text-align: left;
        }
    </style>
</head>

<body>
    <div style="padding-bottom: 2em" class="invoice-box row">
        <table style="direction: rtl;" class="col-md-6" cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="8">
                    <table>
                        <tr>
                            <td style="border-left:0.4px solid rgb(221, 217, 217);text-align:center" class="title">
                                <img src="{{ $image }}"
                                    style="width: 100%; max-width: 150px;margin-top: 10px;" />
                            </td>

                            <td style="text-align: right; padding-left: 10px;font-size: 11px;">
                                شركة سفر تكـ<br />
                                هاتف: {{ $phone }}<br />
                                <span> البريد: {{ $email }} </span><br />
                                العنوان: {{ $address }}
                                <br>
                                 رقم التسجيل الضريبي: {{ $commercial_registration }}
                            </td>
                            <td style="text-align: left; padding-right: 10px;font-size: 11px;">
                                Safar Tech Company<br />
                                Phone: {{ $phone }}<br />
                                email: {{ $email }}<br />
                                Address: {{ $address }} <br />
                                Tax registration number : {{  $commercial_registration }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="8">
                    <table>
                        <tr>
                            <td style="text-align: center;">
                                <h3 style="color: orangered;font-size: 18px;">
                                    فاتورة ضريبية مبسطة<br />
                                    Simplified tax invoice
                                </h3>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="8">
                    <table>
                        <tr style="">
                            <td style="text-align: right; padding-right: 10px;font-size: 12px;">
                                التاريخ <br />
                                اسم العميل
                                <br />
                                رقم الفاتورة
                                <br />
                                رقم الحجز المرجعي
                            </td>

                            <td style="text-align: center;font-size: 12px;">
                                {{ $date->todatestring() }}<br />
                                {{ $user['name'] }}
                                <br />
                                {{ $purchaseOrderNumber }}
                                <br />
                                {{ $booking_reference_id ?? '---' }}
                            </td>

                            <td style="text-align: left; padding-left: 10px;font-size: 12px;">
                                Date <br />
                                Client Name
                                <br />
                                Invoice Number
                                <br />
                                Booking Reference ID
                            </td>
                                Date <br />
                                Client Name <br />
                                Invoice No
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>

            <tr style="text-align: center" class="heading">
                <td style="text-align: center;font-size: 12px;">#</td>

                <td style="text-align: center;font-size: 12px;">Service <br /> الخدمة</td>

                <td style="text-align: center;font-size: 12px;">Service Desc <br /> وصف الخدمة</td>

                <td style="text-align: center;font-size: 12px;">Unit Price <br /> سعر الوحدة </td>

                <td style="font-size: 12px;">  المبلغ الخاضع للضريبة
                    <br>
                    Taxable amount
                </td>

                <td style="text-align: center;font-size: 12px;">Tax rate <br /> معدل الضريبة </td>

                <td style="text-align: center;font-size: 12px;">Tax amount <br /> مبلغ الضريبة </td>

                <td style="text-align: center;font-size: 12px;">Total amount <br /> المبلغ الاجمالي </td>
            </tr>
            @php
                $key = 0;
            @endphp
            @foreach ($items as $item)
                <tr style="text-align: center" class="details">
                    <td style="text-align: center;font-size: 12px;">{{ ++$key }}</td>
                    <td style="text-align: center;font-size: 12px;">
                        {{ $item['passenger'] }}
                        <br>
                        ({{ isset($from) ? $from . ' - ' : '' }} {{ $to }})
                    </td>
                    <td style="text-align: center;font-size: 12px;">{{ $item['description'] }}</td>
                    <td style="text-align: center;font-size: 12px;">{{ $item['unitPrice'] }}</td>
                    <td style="text-align: center;font-size: 12px;">
                        @if($item['first_tax_amount'] == 0)
                            0
                        @else
                        {{ $item['unitPrice'] }}
                        @endif
                    </td>
                    <td style="text-align: center;font-size: 12px;">{{ $item['first_tax_rate'] }}</td>
                    <td style="text-align: center;font-size: 12px;">{{ $item['first_tax_amount'] }}</td>
                    <td style="text-align: center;font-size: 12px;">
                        {{ number_format($item['first_tax_amount'] + $item['unitPrice'], 2) }}</td>
                </tr>
                <tr style="text-align: center" class="details">
                    <td style="text-align: center;font-size: 12px;">{{ ++$key }}</td>
                    <td style="text-align: center;font-size: 12px;">ضرائب ورسوم - Taxes and fees</td>
                    <td style="text-align: center;font-size: 12px;">رسوم ادارية - Administrative fees</td>
                    <td style="text-align: center;font-size: 12px;">{{ $item['administrative_tax_amount'] }}</td>
                    <td style="text-align: center;font-size: 12px;">{{ $item['administrative_tax_amount'] }}</td>
                    <td style="text-align: center;font-size: 12px;">{{ $item['second_tax_rate'] }}</td>
                    <td style="text-align: center;font-size: 12px;">{{ $item['second_tax_amount'] }}</td>
                    <td style="text-align: center;font-size: 12px;">
                        {{ $item['administrative_tax_amount'] + $item['second_tax_amount'] }}</td>
                </tr>
            @endforeach
            <tr class="">
                <td colspan="4">
                    <hr>
                </td>
            </tr>

            <tr class="item">
                <td style="font-size: 12px;" colspan="3">الاجمالي بدون ضريبة القيمة المضافة
                    <br>Total without VAT
                </td>

                <td style="font-size: 12px;" >{{ $totalWithoutVAT }}</td>
            </tr>

            <tr class="item">
                <td style="font-size: 12px;" colspan="3"> اجمالي المبلغ الخاضع للضريبة
                    <br>
                    Total Taxable amount
                </td>

                <td style="font-size: 12px;" >{{ $totalTaxableAmount }}</td>
            </tr>

            <tr class="item">
                <td style="font-size: 12px;" colspan="3"> إجمالي مبلغ ضريبة القيمة المضافة
                    <br>
                    Total VAT Amount
                </td>

                <td style="font-size: 12px;" >{{ $totalVATAmount }}</td>
            </tr>

            <tr class="item">
                <td style="font-size: 12px;" colspan="3"> الاجمالي شامًلا ضريبة القيمة المضافة
                    <br>
                    Total Including VAT
                </td>

                <td style="font-size: 12px;"  colspan="4">{{ $totalIncludingVAT }}</td>
            </tr>
            <tr>
                <td style="font-size: 12px;" colspan="4">
                    {{ $arabic . ' ريال' }}
                </td>
                <td>
                </td>
                <td style="font-size: 12px;" colspan="4">
                    {{ $english . ' SAR' }}
                </td>
            </tr>
            <tr class="">
                <td colspan="8">
                    <hr>
                </td>
            </tr>
        </table>


    </div>
    <div style="border: 0;height:auto;border:none;position: fixed;bottom:0" class="invoice-box second row">
        <table style="position: fixed;bottom:0;width:100%;border:none" class="col-md-6" cellpadding="0" cellspacing="0">
            <tr style="border:none" class="item last-itemm">
                <td style="font-size: 0px;text-align:left">
                    <img src="{{public_path('assets/images/logo.jpeg')}}" alt="Stamp"
                        style="width: 100px; height: 100px;">
                </td>
                <td style="font-size: 0px;text-align:right" colspan="2">
                    {!! $qr !!}
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
