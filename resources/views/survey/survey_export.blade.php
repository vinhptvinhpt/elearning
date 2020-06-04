<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{$dataModel->survey->code}}
        - {{$dataModel->survey->name}}</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
</head>
<body style="font-family: DejaVu Sans;">
<div class="container">
    <div class="row">
        <div class="col-sm-12"
             style="text-align: center; font-size: 20px; font-weight: bold;">{{$dataModel->survey->code}}
            - {{$dataModel->survey->name}}</div>
        <div class="col-sm-12"
             style="text-align: center; font-size: 16px; font-weight: bold;">Bắt
            đầu: {{$dataModel->survey->startdate}}
        </div>
        <div class="col-sm-12"
             style="text-align: center; font-size: 16px; font-weight: bold;">Kết thúc : {{$dataModel->survey->enddate}}
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-sm">
            <div class="table-wrap">
                @foreach($dataModel->statistics as $key => $data)
                    @if($data->type_question===\App\TmsQuestion::MULTIPLE_CHOICE)
                        <div class="col-12 form-group">
                            <!--hien thi cau hoi theo dang cau hoi phia tren, dap an o duoi -->
                            <h5>Câu hỏi {{$key + 1}}: {!!$data->question_content!!}</h5><br/>
                            <div class="col-12 col-lg-9">
                                <div class="col-12 form-group">
                                    @foreach($data->lstQuesChild[0]->lstAnswers as $key_ans => $dataAns)
                                        <div class="custom-control custom-control-inline mb-3">
                                            <label style="font-size: 14px;">
                                                ( {{$dataAns->total_choice}}
                                                / {{$data->lstQuesChild[0]->total_choice}}
                                                )</label>
                                            <label style="font-size: 12px;">
                                                {{$dataAns->answer_content}}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    @else
                        <div class="col-12 form-group">
                            <!--hien thi cau hoi theo dang cau hoi phia tren, dap an o duoi -->
                            <h5>Câu hỏi nhóm số {{$key + 1}}: {!!$data->question_content!!}</h5><br/>

                            <div class="col-12 form-group">
                                @foreach($data->lstQuesChild as $key_child => $dataChild)
                                    <div class="row"><!--hien thi cau hoi theo dang hang ngang -->
                                        <div class="col-12 col-md-4"><h5>{!!$dataChild->question_content!!}</h5></div>
                                        <div class="col-12 col-md-8">
                                            <div class="col-12 form-group">
                                                @foreach($dataChild->lstAnswers as $key_ans => $dataAns)
                                                    <div class="custom-control custom-radio custom-control-inline mb-3 radio-primary">
                                                        <label style="font-size: 14px;">
                                                            ( {{$dataAns->total_choice}}
                                                            / {{$dataChild->total_choice}}
                                                            )</label>
                                                        <label style="font-size: 12px;">
                                                            {{$dataAns->answer_content}}</label>
                                                    </div>
                                                @endforeach

                                            </div>
                                        </div>
                                    </div>

                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach

            </div>
        </div>
    </div>
    <div class="row">
        <br/>
        <h3>Thống kê phản hồi của người dùng</h3>

        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Câu hỏi</th>
                <th scope="col">Người phản hồi</th>
                <th scope="col">Phản hồi</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dataModel->lstFeedback as $key => $feedback)
                <tr>
                    <th scope="row">{{$key+1}}</th>
                    <td>{!!$feedback->content!!}</td>
                    <td>{{$feedback->username}}</td>
                    <td>{!! htmlspecialchars($feedback->content_answer)!!}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
</div>
</body>
</html>
