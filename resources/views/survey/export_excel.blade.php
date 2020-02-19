<!DOCTYPE html>
<html lang="el">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{$dataModel->survey->code}}
        - {{$dataModel->survey->name}}</title>

</head>
<body>

<h3>{{$dataModel->survey->code}}
    - {{$dataModel->survey->name}}</h3>
<h4>Bắt đầu {{$dataModel->survey->startdate}}</h4>
<h4>Kết thúc {{$dataModel->survey->enddate}}</h4>
<br><br>
<table border="1">
    @foreach($dataModel->statistics as $key => $data)
        @if($data->type_question===\App\TmsQuestion::MULTIPLE_CHOICE)
            <tr>
                <th>Câu hỏi {{$key + 1}}</th>
                <td>{!!$data->question_content!!}</td>
            </tr>
            @foreach($data->lstQuesChild[0]->lstAnswers as $key_ans => $dataAns)
                <tr>
                    <th>Đáp án {{$key_ans + 1}}</th>
                    <td> ( {{$dataAns->total_choice}}
                        / {{$data->lstQuesChild[0]->total_choice}})
                    </td>
                    <td>  {!!htmlspecialchars($dataAns->answer_content)!!}
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <th>Câu hỏi nhóm số {{$key + 1}}</th>
                <td>{!!$data->question_content!!}</td>
            </tr>
            @foreach($data->lstQuesChild as $key_child => $dataChild)
                <tr>
                    <th>{!!htmlspecialchars($dataChild->question_content)!!}</th>
                </tr>
                @foreach($dataChild->lstAnswers as $key_ans => $dataAns)
                    <tr>
                        <th>Đáp án {{$key_ans + 1}}</th>
                        <td> ( {{$dataAns->total_choice}}
                            / {{$dataChild->total_choice}}
                            )
                        </td>
                        <td>   {!!htmlspecialchars($dataAns->answer_content)!!}
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <th></th>
                    <td></td>
                </tr>
            @endforeach
        @endif
        <tr>
            <th></th>
            <td></td>
        </tr>
    @endforeach
</table>
<h3>Thống kê phản hồi của người dùng</h3>
<table border="1">

    <tr>
        <th>Câu hỏi</th>
        <th>Người phản hồi</th>
        <th>Phản hồi</th>
    </tr>
    @foreach($dataModel->lstFeedback as $key => $feedback)
        <tr>
            <td>{!!$feedback->content!!}</td>
            <td>{{$feedback->username}}</td>
            <td>{!! htmlspecialchars($feedback->content_answer)!!}</td>
        </tr>
    @endforeach

    <tr>
        <th></th>
        <td></td>
    </tr>

</table>

</body>
</html>
