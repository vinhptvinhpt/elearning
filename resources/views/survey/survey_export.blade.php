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
</head>
<body style="font-family: DejaVu Sans;">
<div class="container">
    <div class="row">
        <div class="col-sm-12"
             style="text-align: center; font-size: 20px; font-weight: bold;">{{$responseModel->survey->code}}
            - {{$responseModel->survey->name}}</div>
    </div>
</div>
<br/>
<table id="datable_2" class="table_res" border="1">
    <thead>
    <tr>
        <th></th>
        @foreach($responseModel->message as $hd)
            <th style="word-wrap: break-word;">{!! $hd->ques_content !!}</th>
        @endforeach

    </tr>
    </thead>
    <tbody>

    @foreach($responseModel->otherData as $data)
        <tr>
            <td>{{$data['email']}}</td>
            @foreach($responseModel->message as $hd)
                @foreach($data['lstData'] as $dt)
                    @if($dt['ques_id'] == $hd->ques_id)
                        <td>{!!$dt['ans_content']!!}</td>
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
