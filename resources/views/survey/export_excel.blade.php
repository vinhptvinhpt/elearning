<!DOCTYPE html>
<html lang="el">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{$survey->code}}
        - {{$survey->name}}</title>
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
<body>

<h3>{{$survey->code}}
    - {{$survey->name}}</h3>
<br/>
<h4>Course: {{$course_info}}</h4>
<br><br>
<table class="table table-bordered">
    <thead>
    <tr>
        <th colspan="3" rowspan="2"></th>
        @foreach($header as $hd)
            <th colspan="2" rowspan="2" style="word-wrap: break-word;">{!! $hd->ques_content !!}</th>
        @endforeach

    </tr>
    </thead>
    <tbody>
    <tr></tr>
    <tr></tr>
    @foreach($dataModel as $data)
        <tr>
            <th colspan="3">{{$data['email']}}</th>
            @foreach($header as $hd)
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
