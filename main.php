<?php

//error_reporting(0);

function output($rtn) {
    header('Content-Type: application/json');
    echo json_encode($rtn);
}

function normalize($time) {
    $parts = explode(':', $time);
    
    if (substr($time, -2) == "pm" && $parts[0] < 12)
        $parts[0] += 12;
    
    if (strlen($parts[0]) == 1)
        $parts[0] = '0' . $parts[0];
    
    return $parts[0] . ':' . substr($parts[1], 0, -3);
}

$semester = "Fall";
if ($_GET['sem'] == 'winter')
    $semester = "Winter";

if (isset($_GET['id']) && isset($_GET['num'])) {
    require_once 'dom.php';
    
    /*
    winter - 10
    fall   - 90
    summer - 50
    */
    
    $year = date("Y");
    $term = $year . '90';
    if ($semester == 'Winter')
        $term = ($year + 1) . '10';

    $final = [
        'exists'   => false,
        'lectures' => [],
        'labs'     => [],
        'title'    => '',
        'course'   => ''
    ];
    
    $name = strtoupper($_GET['id']);
    $num = $_GET['num'];

    $html = file_get_html('https://aurora.umanitoba.ca/banprod/bwckschd.p_get_crse_unsec', false, stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => 'term_in=' . $term . '&sel_subj=dummy&sel_day=dummy&sel_schd=dummy&sel_insm=dummy&sel_camp=dummy&sel_levl=dummy&sel_sess=dummy&sel_instr=dummy&sel_ptrm=dummy&sel_attr=dummy&sel_subj=' . $name . '&sel_crse=' . $num . '&sel_title=&sel_schd=%25&sel_insm=%25&sel_from_cred=&sel_to_cred=&sel_camp=%25&sel_levl=%25&sel_ptrm=%25&sel_dunt_code=&sel_dunt_unit=&sel_instr=%25&sel_attr=%25&begin_hh=0&begin_mi=0&begin_ap=a&end_hh=0&end_mi=0&end_ap=a'
        ]
    ]));

    $id = 1;

    $els = $html->find('.datadisplaytable td .datadisplaytable');
    $crns = $html->find('.ddtitle a');
    $len = count($els);

    if ($len == 0) {
        output($final);
        exit;
    }

    $title = explode(' - ', $crns[0]->innertext)[0];

    $lectures = [];
    $labs = [];

    for ($i = 0; $i < $len; $i++) {
        $section = array_map(function($el) {
            return $el->plaintext;
        }, $els[$i]->find('tr')[1]->find('td'));
        
        $crn = explode('crn_in=', $crns[$i]->href)[1];
        
        $credits = explode(' Credits',
            explode('<br>        ', $els[$i]->parent()->innertext)[1])[0];

        if ($section[1] == "TBA")
            continue;
         
        $times = $section[1];
        $interval = explode(' - ', $times);
        
        $el = [
            'begin'      => normalize($interval[0]),
            'end'        => normalize($interval[1]),
            'crn'        => $crn,
            'times'      => $times,
            'days'       => $section[2],
            'location'   => $section[3],
            'instructor' => $section[6]
        ];
        
        if ($credits == "0.000")
            $labs[] = $el;
        else
            $lectures[] = $el;
    }

    $final = [
        'exists'   => true,
        'lectures' => $lectures,
        'labs'     => $labs,
        'title'    => $title,
        'course'   => $name . ' ' . $num
    ];

    output($final);
    exit;
}

?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>UM Schedule</title>
        <link rel="stylesheet" type="text/css" href="style.css">
        <script src="script.js"></script>
        <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    </head>
    <body>
        <div id="content" width="300px">
            <select name="course_code" id="course_code" style="width:220px"><option value="ACS">ACS - Aboriginal Counselling Skills</option><option value="ATP">ATP - Academic Test Preparation</option><option value="ACC">ACC - Accounting</option><option value="ACT">ACT - Actuarial Studies, Warren Cntr</option><option value="ADED">ADED - Adult Education</option><option value="ABIZ">ABIZ - AgBusiness and AgEconomics</option><option value="AGRI">AGRI - Agriculture</option><option value="DAGR">DAGR - Agriculture Diploma</option><option value="AGEC">AGEC - Agroecology</option><option value="ASLL">ASLL - American Sign Language</option><option value="ANSC">ANSC - Animal Science</option><option value="ANTH">ANTH - Anthropology</option><option value="ABA">ABA - Applied Behaviour Analysis</option><option value="AHS">AHS - Applied Health Sciences</option><option value="ARA">ARA - Arabic</option><option value="ARCH">ARCH - Architecture</option><option value="ARCG">ARCG - Architecture Interdisciplinary</option><option value="ARTS">ARTS - Arts Interdisciplinary</option><option value="ASIA">ASIA - Asian Studies</option><option value="BGEN">BGEN - Biochem. and Medical Genetics</option><option value="BIOL">BIOL - Biological Sciences</option><option value="BME">BME - Biomedical Engineering</option><option value="BIOE">BIOE - Biosystems Engineering</option><option value="BTEC">BTEC - Biotechnology</option><option value="CDN">CDN - Canadian Studies</option><option value="CATH">CATH - Catholic Studies</option><option value="CUCA">CUCA - Cert in Univ and College Admin</option><option value="CTSL">CTSL - Cert Teach Engl as Second Lang</option><option value="CHEM">CHEM - Chemistry</option><option value="CFSW">CFSW - Child &amp; Family Services Worker</option><option value="CITY">CITY - City Planning</option><option value="CIVL">CIVL - Civil Engineering</option><option value="CLAS">CLAS - Classical Studies</option><option value="CHSC">CHSC - Community Health Sciences</option><option value="COMP">COMP - Computer Science</option><option value="CONV">CONV - Conversational Languages</option><option value="COUN">COUN - Counselling</option><option value="DDSS">DDSS - Dental Diagnostic and Surgical</option><option value="HYGN">HYGN - Dental Hygiene</option><option value="DENT">DENT - Dentistry</option><option value="DS">DS - Disability Studies (Grad St.)</option><option value="ECON">ECON - Economics</option><option value="EDUA">EDUA - Education Admin, Fndns &amp; Psych</option><option value="EDUB">EDUB - Education Curric, Tchg, &amp; Lrng</option><option value="EDUC">EDUC - Education Ph.D. Courses</option><option value="EDTC">EDTC - Education Technology</option><option value="ECE">ECE - Electr. and Computer Engin.</option><option value="ENG">ENG - Engineering</option><option value="ENGL">ENGL - English</option><option value="ELSG">ELSG - English Lang Studies Grammar</option><option value="ELSO">ELSO - English Lang Studies Other</option><option value="ELSR">ELSR - English Lang Studies Reading</option><option value="ELSS">ELSS - English Lang Studies Speaking</option><option value="ENTM">ENTM - Entomology</option><option value="ENTR">ENTR - Entrepreneurship/Small Bus.</option><option value="EVLU">EVLU - Envir. Design Landsc &amp; Urban</option><option value="ENVR">ENVR - Environment</option><option value="EER">EER - Environment, Earth &amp; Resources</option><option value="EVAR">EVAR - Environmental Architecture</option><option value="EVDS">EVDS - Environmental Design</option><option value="EVIE">EVIE - Environmental Interior Environ</option><option value="EXED">EXED - Executive Education</option><option value="FMLY">FMLY - Family Social Sciences</option><option value="FILM">FILM - Film Studies</option><option value="FINC">FINC - Finance</option><option value="FIN">FIN - Finance</option><option value="FAAH">FAAH - Fine Art, Art History Courses</option><option value="FA">FA - Fine Art, General Courses</option><option value="STDO">STDO - Fine Art, Studio Courses</option><option value="FOOD">FOOD - Food Science</option><option value="FDNT">FDNT - Foods and Nutr Grad Studies</option><option value="FORS">FORS - Forensic Science</option><option value="FRAN">FRAN - Francais St. Boniface</option><option value="FREN">FREN - French</option><option value="GMGT">GMGT - General Management</option><option value="GEOG">GEOG - Geography</option><option value="GEOL">GEOL - Geological Sciences</option><option value="GRMN">GRMN - German</option><option value="GPE">GPE - Global Political Economy</option><option value="GRAD">GRAD - Graduate Studies</option><option value="GRK">GRK - Greek</option><option value="HEAL">HEAL - Health Studies</option><option value="HEB">HEB - Hebrew</option><option value="CHRD">CHRD - Higher Edu Research and Develt</option><option value="HIST">HIST - History</option><option value="HORT">HORT - Horticulture</option><option value="ANAT">ANAT - Human Anat. and Cell Science</option><option value="HMEC">HMEC - Human Ecology General</option><option value="HNSC">HNSC - Human Nutritional Sciences</option><option value="HRIR">HRIR - Human Res. Mgmt/Indus Relat.</option><option value="HRM">HRM - Human Resource Management</option><option value="HUNG">HUNG - Hungarian</option><option value="ICEL">ICEL - Icelandic</option><option value="IMMU">IMMU - Immunology</option><option value="IDM">IDM - Interdisciplinary Management</option><option value="IMED">IMED - Interdisciplinary Medicine</option><option value="IDES">IDES - Interior Design</option><option value="INTB">INTB - International Business</option><option value="INTL">INTL - International Studies - CUSB</option><option value="ITLN">ITLN - Italian</option><option value="JUD">JUD - Judaic Civilization</option><option value="KPER">KPER - Kinesio, Phys Ed, &amp; Recreation</option><option value="KIN">KIN - Kinesiology</option><option value="LABR">LABR - Labour Studies</option><option value="LARC">LARC - Landscape Architecture</option><option value="LATN">LATN - Latin</option><option value="LAW">LAW - Law</option><option value="LEAD">LEAD - Leadership</option><option value="LDRS">LDRS - Leadership (Extended Ed.)</option><option value="LB">LB - Library Informatics</option><option value="LING">LING - Linguistics</option><option value="MGMT">MGMT - Management (Extended Ed.)</option><option value="MIS">MIS - Management Info. Systems</option><option value="PHDM">PHDM - Management Ph.D.</option><option value="MSCI">MSCI - Management Science</option><option value="MKT">MKT - Marketing</option><option value="MSKL">MSKL - Math Skills</option><option value="MATH">MATH - Mathematics</option><option value="MECG">MECG - Mech. Engineering Graduate</option><option value="MECH">MECH - Mechanical Engineering</option><option value="MMIC">MMIC - Medical Microbiology</option><option value="REHB">REHB - Medical Rehabilitation</option><option value="MBIO">MBIO - Microbiology</option><option value="MDFY">MDFY - Midwifery</option><option value="MUSC">MUSC - Music</option><option value="NATV">NATV - Native Studies</option><option value="NRI">NRI - Natural Resource Management</option><option value="NURS">NURS - Nursing</option><option value="OT">OT - Occupational Therapy</option><option value="OPER">OPER - Operations and Process Mgmt</option><option value="OPM">OPM - Operations Management</option><option value="ORLB">ORLB - Oral Biology</option><option value="PATH">PATH - Pathology</option><option value="PEAC">PEAC - Peace and Conflict Studies</option><option value="PHAC">PHAC - Pharmacology</option><option value="PHRM">PHRM - Pharmacy</option><option value="PHIL">PHIL - Philosophy</option><option value="PERS">PERS - Phys Ed &amp; Rec Studies General</option><option value="PHED">PHED - Physical Education</option><option value="PT">PT - Physical Therapy</option><option value="PAEP">PAEP - Physician Assistant Education</option><option value="PHYS">PHYS - Physics and Astronomy</option><option value="PHGY">PHGY - Physiology</option><option value="PLNT">PLNT - Plant Science</option><option value="POL">POL - Polish (Slavic Studies)</option><option value="POLS">POLS - Political Studies</option><option value="PORT">PORT - Portuguese</option><option value="PDSD">PDSD - Preventive Dental Science</option><option value="PDEV">PDEV - Professional Development</option><option value="PSYC">PSYC - Psychology</option><option value="REC">REC - Recreation Studies</option><option value="RLGN">RLGN - Religion</option><option value="RESP">RESP - Respiratory Therapy</option><option value="RSTD">RSTD - Restorative Dentistry</option><option value="RUSN">RUSN - Russian (Slavic Studies)</option><option value="SLAV">SLAV - Slavic Studies (Pol,Rusn,Ukrn)</option><option value="SWRK">SWRK - Social Work</option><option value="SOC">SOC - Sociology</option><option value="SOIL">SOIL - Soil Science</option><option value="SPAN">SPAN - Spanish</option><option value="STAT">STAT - Statistics</option><option value="SSND">SSND - Summer Session Non-Degree</option><option value="SCM">SCM - Supply Chain Management</option><option value="SURG">SURG - Surgery</option><option value="TXSC">TXSC - Textile Sciences</option><option value="THTR">THTR - Theatre</option><option value="TRAD">TRAD - Traduction (St. Boniface)</option><option value="TRNS">TRNS - Transport Institute</option><option value="UKRN">UKRN - Ukrainian (Slavic Studies)</option><option value="UCHS">UCHS - Ukrainian Cdn Heritage Studies</option><option value="UGME">UGME - Undergrad. Medical Education</option><option value="UCA">UCA - University and College Admin</option><option value="WOMN">WOMN - Women's and Gender Studies</option><option value="YDSH">YDSH - Yiddish</option></select>
            <input type="text" id="course_name" placeholder="number" style="width:70px">
            <input type="button" id="add_course" value="Add course">
            <input type="button" id="remove_course" value="Remove course" disabled>
            <select size="5" id="courses">
                <optgroup label="Courses">
                    <option disabled>Courses that you add will appear here</option>
                </optgroup>
            </select>
            <select size="5" id="lectures">
                <optgroup label="Lecture Sections">
                    <option disabled>Please select a course</option>
                </optgroup>
            </select>
            <select size="5" id="labs">
                <optgroup label="Lab Sections">
                    <option disabled>Please select a course</option>
                </optgroup>
            </select>
            <select size="5" id="conflicts">
                 <optgroup label="Conflicts">
                      <option disabled>No conflicts</option>
                 </optgroup>
            </select>
            <textarea id="raw" readonly>Courses and CRNs are displayed here</textarea>
        </div>
        <table>
            <td id="sidebar" style="min-width:310px"></td>
            <td style="margin-left:325px">
                <h2>University of Manitoba Scheduler (<?php echo $semester; ?> Semester)</h2>
                <noscript><span>Please enable JavaScript to use UM Scheduler</span></noscript>
                <div id="calendar"></div>
            </td>
        </table>
    </body>
</html>
