<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Report</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href='https://fonts.googleapis.com/css2?family=Cairo' rel='stylesheet'>
    <style>
        .msg {
            padding: 10px;
            background: #222;
            position: relative;
        }
        .msg h1 {
            color: #fff;
        }

        .msg a {
            margin-left: 20px;
            background: #408814;
            color: white;
            padding: 4px 8px;
            text-decoration: none;
        }

        .msg a:hover {
            background: #266400;
        }

        /* //-- yui-grids style overrides -- */
        body {
            font-family: Georgia;
            color: #444;
        }

        #inner {
            padding: 10px 80px;
            margin: 80px auto;
            background: #f5f5f5;
            border: solid #666;
            border-width: 8px 0 2px 0;
        }

        .yui-gf {
            margin-bottom: 2em;
            padding-bottom: 2em;

        }

        /* //-- header, body, footer -- */
        #hd {
            margin: 2.5em 0 3em 0;
            padding-bottom: 1.5em;

        }

        #hd h2 {
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        #bd, #ft {
            margin-bottom: 2em;
        }

        /* //-- footer -- */
        #ft {
            padding: 1em 0 5em 0;
            font-size: 92%;
            border-top: 1px solid #ccc;
            text-align: center;
        }

        #ft p {
            margin-bottom: 0;
            text-align: center;
        }

        /* //-- core typography and style -- */
        #hd h1 {
            font-size: 48px;
            text-transform: uppercase;
            letter-spacing: 3px;
        }

        h2 {
            font-size: 152%
        }

        h3, h4 {
            font-size: 122%;
        }

        h1, h2, h3, h4 {
            color: #333;
        }

        p {
            font-size: 100%;
            line-height: 18px;
            padding-right: 3em;
        }

        a {
            color: #990003
        }

        a:hover {
            text-decoration: none;
        }

        strong {
            font-weight: bold;
        }

        li {
            line-height: 24px;

        }

        p.enlarge {
            font-size: 144%;
            padding-right: 6.5em;
            line-height: 24px;
        }

        p.enlarge span {
            color: #000
        }

        .contact-info {
            margin-top: 7px;
        }

        .first h2 {
            font-style: italic;
        }




        /* //-- section styles -- */

        a#pdf {
            display: block;
            float: left;
            background: #666;
            color: white;
            padding: 6px 50px 6px 12px;
            margin-bottom: 6px;
            text-decoration: none;
        }

        a#pdf:hover {
            background: #222;
        }

        .job {
            position: relative;
            margin-bottom: 1em;
            padding-bottom: 1em;

        }

        .job h4 {
            position: absolute;
            top: 0.35em;
            right: 0
        }

        .job p {
            margin: 0.75em 0 3em 0;
        }

        .last {
            border: none;
        }

        .skills-list {
        }

        .skills-list ul {
            margin: 0;
        }

        .skills-list li {
            margin: 3px 0;
            padding: 3px 0;
        }

        .skills-list li span {
            font-size: 152%;
            display: block;
            margin-bottom: -2px;
            padding: 0
        }

        .talent {
            width: 32%;
            float: left
        }

        .talent h2 {
            margin-bottom: 6px;
        }

        #srt-ttab {
            margin-bottom: 100px;
            text-align: center;
        }

        #srt-ttab img.last {
            margin-top: 20px
        }

        /* --// override to force 1/8th width grids -- */
        .yui-gf .yui-u {
            width: 80.2%;
        }

        .yui-gf div.first {
            width: 12.3%;
        }


    </style>
</head>

<body style="font-family: DejaVu Sans, sans-serif ;direction: rtl;" >

<div id="doc2" class="yui-t7">
    <div id="inner">

        <div id="hd">
            <div class="yui-gc">
                <div class="yui-u">
                    <div class="row">

                        <div class="col-md-11"> <h1>{{$data['personal_data']->full_name}}</h1></div>
                        <div class="col-md-1">
                            <img src="https://res.cloudinary.com/dxaqyslkq/image/upload/w_100,q_100/v1581928924/{{$data['personal_data']->image}}">
                        </div>
                    </div>

                </div>
                <div class="yui-u">
                    <div class="contact-info">
                        @if($data['personal_data']->Nationality)
                            <div
                                class="elementor-element elementor-element-ef1a90f elementor-widget elementor-widget-heading"
                                data-id="ef1a90f" data-element_type="widget" data-widget_type="heading.default">
                                <div class="elementor-widget-container">
                                    <h6 class="elementor-heading-title elementor-size-default"> الجنسية: {{$data['personal_data']->Nationality->title}}</h6></div>
                            </div>
                        @endif
                        @if($data['personal_data']->date_of_birth)
                            <div
                                class="elementor-element elementor-element-2e715ad elementor-widget elementor-widget-heading"
                                data-id="2e715ad" data-element_type="widget" data-widget_type="heading.default">
                                <div class="elementor-widget-container">
                                    <h6 class="elementor-heading-title elementor-size-default">تاريخ الميلاد: {{$data['personal_data']->date_of_birth}}</h6>
                                </div>
                            </div>
                        @endif
                        @if($data['personal_data']->social_status)
                            <div
                                class="elementor-element elementor-element-fa5f3d0 elementor-widget elementor-widget-heading"
                                data-id="fa5f3d0" data-element_type="widget" data-widget_type="heading.default">
                                <div class="elementor-widget-container">
                                    <h6 class="elementor-heading-title elementor-size-default">الحالة الإجتماعية:
                                        @if($data['personal_data']->social_status == 1) متزوج @else أعزب @endif
                                    </h6>
                                </div>
                            </div>
                        @endif
                        @if($data['personal_data']->address)
                            <div
                                class="elementor-element elementor-element-0603b1e elementor-widget elementor-widget-heading"
                                data-id="0603b1e" data-element_type="widget" data-widget_type="heading.default">
                                <div class="elementor-widget-container">
                                    <h6 class="elementor-heading-title elementor-size-default">محل الإقامة: {{$data['personal_data']->address}}</h6>
                                </div>
                            </div>
                        @endif
                        @if($data['personal_data']->license)
                            <div
                                class="elementor-element elementor-element-eb9cb35 elementor-widget elementor-widget-heading"
                                data-id="eb9cb35" data-element_type="widget" data-widget_type="heading.default">
                                <div class="elementor-widget-container">
                                    <h6 class="elementor-heading-title elementor-size-default">رخصة القايدة: @if($data['personal_data']->license == 1) متاح @else غير متاح @endif</h6>
                                </div>
                            </div>
                        @endif
                    </div><!--// .contact-info -->
                </div>
            </div><!--// .yui-gc -->
        </div><!--// hd -->
        <div class="elementor-widget-wrap elementor-element-populated">
            <div
                class="elementor-element elementor-element-50dabf7 elementor-widget elementor-widget-heading"
                data-id="50dabf7" data-element_type="widget" data-widget_type="heading.default">
                <div class="elementor-widget-container">
                    <strong class="elementor-heading-title elementor-size-default">بيانات الاتصال:</strong>
                </div>
            </div>
            <div
                class="elementor-element elementor-element-49fd032 elementor-widget-divider--view-line elementor-widget elementor-widget-divider"
                data-id="49fd032" data-element_type="widget" data-widget_type="divider.default">
                <div class="elementor-widget-container">
                    <div class="elementor-divider">
                        <span class="elementor-divider-separator"></span>
                    </div>
                </div>
            </div>
            <div
                class="elementor-element elementor-element-1900055 elementor-widget elementor-widget-text-editor"
                data-id="1900055" data-element_type="widget" data-widget_type="text-editor.default">
                <div class="elementor-widget-container">
                    <ul class="elementor-icon-list-items">
                        @if($data['personal_data']->mail)
                            <li class="elementor-icon-list-item">
                                <a href="http://Test@test.com/">
                        <span class="elementor-icon-list-icon">
    <i aria-hidden="true" class="fas fa-id-card"></i>						</span>
                                    <span
                                        class="elementor-icon-list-text">{{$data['personal_data']->mail}}</span>
                                </a>
                            </li>
                        @endif
                        @if($data['personal_data']->phone)
                            <li class="elementor-icon-list-item">
                    <span class="elementor-icon-list-icon">
    <i aria-hidden="true" class="fas fa-phone-alt"></i>						</span>
                                <span
                                    class="elementor-icon-list-text">{{$data['personal_data']->phone}}</span>
                            </li>
                        @endif
                        @if($data['personal_data']->web_site)
                            <li class="elementor-icon-list-item">
                    <span class="elementor-icon-list-icon">
    <i aria-hidden="true" class="fab fa-weebly"></i>						</span>
                                <span
                                    class="elementor-icon-list-text"> {{$data['personal_data']->web_site}} </span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            @if(count($data['job_experience']) > 0)
                <div
                    class="elementor-element elementor-element-50dabf7 elementor-widget elementor-widget-heading"
                    data-id="50dabf7" data-element_type="widget" data-widget_type="heading.default">
                    <div class="elementor-widget-container">
                        <strong class="elementor-heading-title elementor-size-default">الخبرة الوظيفية:</strong>
                    </div>
                </div>
                <div
                    class="elementor-element elementor-element-49fd032 elementor-widget-divider--view-line elementor-widget elementor-widget-divider"
                    data-id="49fd032" data-element_type="widget" data-widget_type="divider.default">
                    <div class="elementor-widget-container">
                        <div class="elementor-divider">
                            <span class="elementor-divider-separator"></span>
                        </div>
                    </div>
                </div>
                <div
                    class="elementor-element elementor-element-1900055 elementor-widget elementor-widget-text-editor"
                    data-id="1900055" data-element_type="widget" data-widget_type="text-editor.default">
                    <div class="elementor-widget-container">
                        @foreach($data['job_experience'] as $row)
                            <ul>
                                <li>{{$row->start_date}} &#8211; {{$row->end_date}} :     
                                     <h3>{{$row->job_name}}</h3> ( {{$row->job_distination}} )
                                </li>
                            </ul>
                            <p>{{$row->job_description}}</p>
                            <p><strong>                              </strong></p>
                        @endforeach
                    </div>
                </div>
            @endif
            @if(count($data['certificat']) > 0)
                <div
                    class="elementor-element elementor-element-5f91984 elementor-widget elementor-widget-heading"
                    data-id="5f91984" data-element_type="widget" data-widget_type="heading.default">
                    <div class="elementor-widget-container">
                        <strong class="elementor-heading-title elementor-size-default">الشهادات:</strong></div>
                </div>
                <div
                    class="elementor-element elementor-element-b539f1d elementor-widget-divider--view-line elementor-widget elementor-widget-divider"
                    data-id="b539f1d" data-element_type="widget" data-widget_type="divider.default">
                    <div class="elementor-widget-container">
                        <div class="elementor-divider">
                            <span class="elementor-divider-separator"></span>
                        </div>
                    </div>
                </div>
                <div
                    class="elementor-element elementor-element-b9814da elementor-widget elementor-widget-text-editor"
                    data-id="b9814da" data-element_type="widget" data-widget_type="text-editor.default">
                    <div class="elementor-widget-container">
                        @foreach($data['certificat'] as $row)
                            <ul>
                                <li>{{$row->start_date}} - {{$row->end_date}} :     
                                     <h3>{{$row->certificate_name}}</h3>
                                </li>
                            </ul>
                            <p>                                                                    
                                  {{$row->degree_specialization}} - {{$row->collage_name}} </p>
                            <p> </p>
                        @endforeach
                    </div>
                </div>
            @endif
            @if(count($data['personal_experience']) > 0)
                <div
                    class="elementor-element elementor-element-7adb9fa elementor-widget elementor-widget-heading"
                    data-id="7adb9fa" data-element_type="widget" data-widget_type="heading.default">
                    <div class="elementor-widget-container">
                        <strong class="elementor-heading-title elementor-size-default">الخبرات الشخصية:</strong>
                    </div>
                </div>
                <div
                    class="elementor-element elementor-element-e19666c elementor-widget-divider--view-line elementor-widget elementor-widget-divider"
                    data-id="e19666c" data-element_type="widget" data-widget_type="divider.default">
                    <div class="elementor-widget-container">
                        <div class="elementor-divider">
                            <span class="elementor-divider-separator"></span>
                        </div>
                    </div>
                </div>
                <div
                    class="elementor-element elementor-element-52cc8bd elementor-widget elementor-widget-text-editor"
                    data-id="52cc8bd" data-element_type="widget" data-widget_type="text-editor.default">
                    <div class="elementor-widget-container">
                        @foreach($data['personal_experience'] as $row)
                            <ul>
                                <li>  <h3>{{$row->job_name}}</h3>
                                </li>
                            </ul>
                            <p>                                   {{$row->job_distination}}</p>
                            <p> </p>
                        @endforeach
                    </div>
                </div>
            @endif
            @if(count($data['hobby']) > 0)
                <div
                    class="elementor-element elementor-element-8e5028a elementor-widget elementor-widget-heading"
                    data-id="8e5028a" data-element_type="widget" data-widget_type="heading.default">
                    <div class="elementor-widget-container">
                        <strong class="elementor-heading-title elementor-size-default">الهوايات:</strong></div>
                </div>
                <div
                    class="elementor-element elementor-element-759b3a0 elementor-widget-divider--view-line elementor-widget elementor-widget-divider"
                    data-id="759b3a0" data-element_type="widget" data-widget_type="divider.default">
                    <div class="elementor-widget-container">
                        <div class="elementor-divider">
                            <span class="elementor-divider-separator"></span>
                        </div>
                    </div>
                </div>
                <div
                    class="elementor-element elementor-element-3e58171 elementor-widget elementor-widget-text-editor"
                    data-id="3e58171" data-element_type="widget" data-widget_type="text-editor.default">
                    <div class="elementor-widget-container">
                        <ul>
                            @foreach($data['hobby'] as $row)
                                <li><h3>{{$row->name}}</h3></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            @if(count($data['course']) > 0)
                <div
                    class="elementor-element elementor-element-d138bfd elementor-widget elementor-widget-heading"
                    data-id="d138bfd" data-element_type="widget" data-widget_type="heading.default">
                    <div class="elementor-widget-container">
                        <strong class="elementor-heading-title elementor-size-default">الكورسات:</strong></div>
                </div>
                <div
                    class="elementor-element elementor-element-18012e6 elementor-widget-divider--view-line elementor-widget elementor-widget-divider"
                    data-id="18012e6" data-element_type="widget" data-widget_type="divider.default">
                    <div class="elementor-widget-container">
                        <div class="elementor-divider">
                            <span class="elementor-divider-separator"> </span>
                        </div>
                    </div>
                </div>
                <div
                    class="elementor-element elementor-element-4369207 elementor-widget elementor-widget-text-editor"
                    data-id="4369207" data-element_type="widget" data-widget_type="text-editor.default">
                    <div class="elementor-widget-container">
                        <ul>
                            @foreach($data['course'] as $row)
                                <li><h3>{{$row->course_name}}</h3></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div><!-- // inner -->
</div><!--// doc -->
</body>
</html>
