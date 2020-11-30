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
        <th colspan="3" rowspan="10"></th>
        @foreach($header as $hd)
            <th colspan="2" rowspan="10" style="word-wrap: break-word;">{{$hd->ques_name}}:{!! $hd->ques_content !!} -
                Section: {{$hd->section_name}}</th>
        @endforeach

    </tr>
    </thead>
    <tbody>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    @foreach($dataModel as $data)
        <tr>
            <th colspan="3" rowspan="4">{{$data['email']}}</th>
            @foreach($header as $hd)
                @foreach($data['lstData'] as $dt)
                    @if($dt['ques_id'] == $hd->ques_id && $dt['section_id'] == $hd->section_id)
                        <td colspan="2" rowspan="4">Total Point: {{$dt['total_point']}} <br/>
                            @if($dt['avg_point'])
                                Average Point: {{number_format($dt['avg_point'],2)}}
                            @else
                                Average Point:
                            @endif
                        </td>
                    @endif
                @endforeach
            @endforeach
        </tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>

    @endforeach

    </tbody>
    <tfoot>

    </tfoot>
</table>

</body>
</html>
