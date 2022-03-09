<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
</head>
<body>
@php
    $headerStyle="
        border: 1px solid #ddd;
        padding: 8px;
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #5f6368;
        color: white;
    ";

    $rowStyle="
    border: 1px solid #ddd;
    padding: 8px;
    ";
@endphp
{{--Hello <strong>{{ $accountDetails }}</strong>,--}}
Hello <strong>{{ $accountDetails->contact->name }}</strong>,

<p>Here's your bill for <strong>{{ $subscription->plan->name }} plan</strong> and due date for the same
    <strong>{{$bill->bill_due_date   }}</strong></p>

<p><strong>Below are your bill details</strong></p>

<table style="border-collapse: collapse;">
    <thead>
    <tr>
        <th scope="col" style=" {{$headerStyle}} ">Bill Number</th>
        <th scope="col" style=" {{$headerStyle}} ">Bill Issue Date</th>
        <th scope="col" style=" {{$headerStyle}} ">Plan Name</th>
        <th scope="col" style=" {{$headerStyle}} ">Plan Validity</th>
        <th scope="col" style=" {{$headerStyle}} ">Fees</th>
        <th scope="col" style=" {{$headerStyle}} ">Previous Outstanding Amount</th>
        <th scope="col" style=" {{$headerStyle}} ">Total Due Amount</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td style="{{$rowStyle}}"> {{ $bill->bill_number }} </td>
        <td style="{{$rowStyle}}"> {{ $bill->bill_issued_date }} </td>
        <td style="{{$rowStyle}}"> {{ $subscription->plan->name  }} </td>
        <td style="{{$rowStyle}}"> {{ $subscription->plan->validity  }} </td>
        <td style="{{$rowStyle}}"> {{ $subscription->charge  }} </td>
        <td style="{{$rowStyle}}"> {{ $bill->prev_due_amount }} </td>
        <td style="{{$rowStyle}}"> {{ $bill->bill_amount }} </td>
    </tr>

    </tbody>
</table>

<p>Regards,</p>
<p>Blaze Gym App</p>
<p>Developed by (http://aaivatech.com/)</p>

</body>
</html>

