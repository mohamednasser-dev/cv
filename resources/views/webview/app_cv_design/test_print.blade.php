<!DOCTYPE html>
<html lang="en">
<head>
    <!-- <style>
     @page { size: 500pt 500pt; }
     </style> -->
    <!-- <meta charset="utf-8"> -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Report</title>
{{--    <meta http-equiv="content-type" content="text/html; charset=utf-8" />--}}

{{--    <meta name="keywords" content="" />--}}
{{--    <meta name="description" content="" />--}}

{{--    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/reset-fonts-grids/reset-fonts-grids.css" media="all" />--}}
{{--    <link rel="stylesheet" type="text/css" href="/cv_design/default_design/resume.css" media="all" />--}}

<!-- Bootstrap -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href='https://fonts.googleapis.com/css2?family=Cairo' rel='stylesheet'>


    <style>
        /*
    ---------------------------------------------------------------------------------
        STRIPPED DOWN RESUME TEMPLATE
        html resume

        v0.9: 5/28/09

        design and code by: thingsthatarebrown.com
                            (matt brown)
    ---------------------------------------------------------------------------------
    */


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
            border-bottom: 1px solid #ccc;
        }

        /* //-- header, body, footer -- */
        #hd {
            margin: 2.5em 0 3em 0;
            padding-bottom: 1.5em;
            border-bottom: 1px solid #ccc
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
            border-bottom: 1px solid #ccc;
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

        .last {
            border-bottom: 0
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
            border-bottom: 1px solid #ccc;
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

<body style="font-family: DejaVu Sans, sans-serif ;">

<div id="doc2" class="yui-t7">
    <div id="inner">

        <div id="hd">
            <div class="yui-gc">
                <div class="yui-u">
                    <h1>{{$data['personal_data']->full_name}}</h1>
                </div>
                <div class="yui-u">
                    <div class="contact-info">
                        <h3><a href="mailto:name@yourdomain.com">{{$data['personal_data']->email}}</a></h3>
                        <h3>{{$data['personal_data']->phone}}</h3>
                        <h3>{{$data['personal_data']->address}}</h3>
                    </div><!--// .contact-info -->
                </div>
            </div><!--// .yui-gc -->
        </div><!--// hd -->
        <div id="bd">
            <div id="yui-main">
                <div class="yui-b">
                    <div class="yui-gf">
                        <div class="yui-u">
                            <h2>personal experience</h2>
                        </div>
                        <div class="yui-u">
                            @foreach($data['personal_experience'] as $row)
                                <div class="talent">
                                    <h2>{{$row->job_name}}</h2>
                                    <p>{{$row->job_distination}}</p>
                                </div>
                            @endforeach
                        </div>
                    </div><!--// .yui-gf -->
                    <div class="yui-gf">
                        <div class="yui-u">
                            <h2>hobbies</h2>
                        </div>
                        <div class="yui-u">
                            <ul class="talent">
                                @foreach($data['hobby'] as $row)
                                <li>{{$row->name}}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div><!--// .yui-gf-->
                    <div class="yui-gf">
                        <div class="yui-u ">
                            <h2>job experience</h2>
                        </div><!--// .yui-u -->
                        <div class="yui-u">
                            @foreach($data['job_experience'] as $row)
                            <div class="job">
                                <h2>{{$row->job_name}}</h2>
                                <h3>{{$row->job_distination}}</h3>
                                <h4>{{$row->start_date}}  -  {{$row->end_date}}</h4>
                            </div>
                            @endforeach
                        </div><!--// .yui-u -->
                    </div><!--// .yui-gf -->
                    <div class="yui-gf">
                        <div class="yui-u ">
                            <h2>certificat</h2>
                        </div><!--// .yui-u -->
                        <div class="yui-u">
                            @foreach($data['certificat'] as $row)
                                <div class="job">
                                    <h2>{{$row->certificate_name}}</h2>
                                    <h3>{{$row->degree_specialization}}</h3>
                                    <h3>{{$row->collage_name}}</h3>
                                    <h4>{{$row->start_date}}  -  {{$row->end_date}}</h4>
                                </div>
                            @endforeach
                        </div><!--// .yui-u -->
                    </div><!--// .yui-gf -->
                    <div class="yui-gf last">
                        <div class="yui-u ">
                            <h2 style="font-weight: 20px;">course</h2>
                        </div><!--// .yui-u -->
                        <div class="yui-u">
                            @foreach($data['course'] as $row)
                                <div class="job">
                                    <h2>{{$row->course_name}}</h2>
                                    <h3>{{$row->degree}}</h3>
                                    <h3>{{$row->collage_name}}</h3>
                                    <h4>{{$row->start_date}}  -  {{$row->end_date}}</h4>
                                </div>
                            @endforeach
                        </div><!--// .yui-u -->
                    </div><!--// .yui-gf -->

                </div><!--// .yui-b -->
            </div><!--// yui-main -->
        </div><!--// bd -->
    </div><!-- // inner -->
</div><!--// doc -->

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> -->
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap.min.js"></script>
</body>
</html>
