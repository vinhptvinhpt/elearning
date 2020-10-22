<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{$responseModel->survey->code}}
        - {{$responseModel->survey->name}}</title>
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <style>
        table {
            border-collapse: collapse;
        }

        table td {
            border: 1px solid gray;
        }

    </style>
</head>
<body style="font-family: DejaVu Sans;">
<div class="container">
    <div class="row">
        <div class="col-sm-12"
             style="text-align: center; font-size: 20px; font-weight: bold;">{{$responseModel->survey->code}}
            - {{$responseModel->survey->name}}</div>
    </div>
    <div class="row">
        <div class="col-sm-12"
             style="text-align: center; font-size: 16px; font-weight: bold;">Course: {{$responseModel->info}}</div>
    </div>
</div>
<br/>
<table class="table table-bordered">
    <thead>
    <tr>
        <th colspan="3" rowspan="2"></th>
        @foreach($responseModel->message as $hd)
            <th scope="col" colspan="2" rowspan="2" style="word-wrap: break-word;">{!! $hd->ques_content !!}</th>
        @endforeach

    </tr>
    </thead>
    <tbody>
    <tr></tr>
    @foreach($responseModel->otherData as $data)
        <tr>
            <th colspan="3" scope="row">{{$data['email']}}</th>
            @foreach($responseModel->message as $hd)
                @foreach($data['lstData'] as $dt)
                    @if($dt['ques_id'] == $hd->ques_id)
                        <td colspan="2">{!!$dt['ans_content']!!}</td>
                    @endif
                @endforeach
            @endforeach
        </tr>
    @endforeach

    </tbody>
    <tfoot>

    </tfoot>
</table>
</body>
</html>
