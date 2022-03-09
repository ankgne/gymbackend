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

<p>We are so glad that you have registered with our gym. Your Registration number is
    <strong>{{ $accountDetails->registration_number }}</strong></p>

<p><strong>Below are your details of the registration</strong></p>

<table style="border-collapse: collapse;">
    <thead>
    <tr>
        <th scope="col" style=" {{$headerStyle}} ">Name</th>
        <th scope="col" style=" {{$headerStyle}} ">Phone Number</th>
        <th scope="col" style=" {{$headerStyle}} ">Email Address</th>
        <th scope="col" style=" {{$headerStyle}} ">Plan Name</th>
        <th scope="col" style=" {{$headerStyle}} ">Plan Start Date</th>
        <th scope="col" style=" {{$headerStyle}} ">Plan Amount</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td style="{{$rowStyle}}"> {{ $accountDetails->contact->name }} </td>
        <td style="{{$rowStyle}}"> {{ $accountDetails->contact->phone }} </td>
        <td style="{{$rowStyle}}"> {{ $accountDetails->contact->email }} </td>
        <td style="{{$rowStyle}}"> {{ $accountDetails->subscriptions[0]->plan->name }} </td>
        <td style="{{$rowStyle}}"> {{ $accountDetails->subscriptions[0]->start_date }} </td>
        <td style="{{$rowStyle}}"> {{ $accountDetails->subscriptions[0]->charge }} </td>
    </tr>

    </tbody>
</table>

<p>Regards,</p>
<p>Blaze Gym App</p>
<p>Developed by (http://aaivatech.com/)</p>

</body>
</html>

