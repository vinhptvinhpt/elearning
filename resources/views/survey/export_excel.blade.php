<!DOCTYPE html>
<html lang="el">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{$survey->code}}
        - {{$survey->name}}</title>
    <link href="/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<h3>{{$survey->code}}
    - {{$survey->name}}</h3>

<br><br>
<table id="datable_2" class="table_res" border="1">
    <thead>
    <tr>
        <th></th>
        @foreach($header as $hd)
            <th style="word-wrap: break-word;">{!! $hd->ques_content !!}</th>
        @endforeach

    </tr>
    </thead>
    <tbody>

    @foreach($dataModel as $data)
        <tr>
            <td>{{$data['email']}}</td>
            @foreach($header as $hd)
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
