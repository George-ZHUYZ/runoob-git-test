<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title></title>
        <style>
            body{padding:0;margin:0;font:normal 14px/25px "\5FAE\8F6F\96C5\9ED1";color:#555;}
            ul,li{margin:0;padding:0;}
            li{list-style:none;}

            .day{width:350px;margin:100px auto;}
            .day table{width:100%;text-align:center;border-collapse:collapse;border-spacing:0;line-height:30px;}
            .day table th{background:#F5F5F5;font-weight:normal;line-height:30px;font-size:14px;}
            .day table td,.day th{border:1px solid #EEE;}
            .day table td:hover{background:#4092CC;color:#FFF;}
            .day table td:nth-child(1),table td:nth-child(7),table th:nth-child(1),table th:nth-child(7){color:#C00}
            .day table td.now{background:#4092CC;color:#FFF;}
            .day table td.past{color:#c2c2c2;}
            .day table td.coming{color:#c2c2c2;}

            .DaySelect{margin-bottom:10px;}
            .DaySelect > div{display:inline-block;#float：left;}
            .DaySelect i{cursor:pointer;#float：left;}
            .DaySelect i:hover{color:#C00;}
            .DaySelect .lr{width:30px;display:inline-block;text-align:center;font-size:18px;}

            .select{width:90px;text-indent:10px;position:relative;}
            .stop{border:1px solid #EEE;}
            .stop:before{content:"";width:8px;height:8px;display:block;float:right;margin-right:10px;margin-top:5px;font-size:0;line-height:0;border:1px solid #AAA;border-left:none;border-top:none;transform:rotate(45deg);}
            .sbox{width:88px;max-height:0;overflow:auto;cursor:pointer;position:absolute;top:25px;left:0;background:#FFF;font-size:12px;text-align:center;}
            .sbox li:hover{background:#EEE;}
            .select:hover .stop{background:#EEE;}
            .select:hover .stop:before{transform:rotate(225deg);margin-top:10px;transition:transform .2s linear}
            .select:hover .sbox{max-height:200px;border:1px solid #EEE;border-top-color:#FFF;;transition:max-height .2s ease-in-out;}

            #cy:after{content:" 年";}
            #cm:after{content:" 月";}
            #sm{width:70px;}
            #mm{width:68px;}
        </style>
    </head>

    <body>
        <div class="day">
            <div class="DaySelect">
                <i class="lr" onclick="Month('l')" title="上一月"><</i>
                <div class="select">
                    <div class="stop" id="cy"><?php echo filter_input(INPUT_POST, 'Y') ? filter_input(INPUT_POST, 'Y') : date('Y'); ?></div>
                    <div class="sbox">
                        <ul id="YearAll">
                            <li><?php echo filter_input(INPUT_POST, 'Y') ? filter_input(INPUT_POST, 'Y') : date('Y'); ?></li>
                        </ul>
                    </div>
                </div>
                <div class="select" id="sm">
                    <div class="stop" id="cm"><?php echo $month = filter_input(INPUT_POST, 'm') ? filter_input(INPUT_POST, 'm') : date('m'); ?></div>
                    <div class="sbox" id="mm">
                        <ul id="DateAll">
                            <li>01</li>
                        </ul>
                    </div>
                </div>
                <i class="lr" onclick="Month('r')" title="下一月">></i>
                <i onclick="now()">今天</i>
            </div>
            <div id="DayAll"></div>
        </div>
    </body>
</html>
<script>
    var SY, SM, SD, cy, cm;
    SY = new Date().getFullYear();
    SM = new Date().getMonth() + 1;
    SD = new Date().getDate();
    cy = document.getElementById("cy");
    cm = document.getElementById("cm");

    window.onload = function () {
        getDynamicTable(SY, SM);
        document.getElementById("YearAll").innerHTML = YearAll(SY);
        document.getElementById("DateAll").innerHTML = DateAll();
    };

    function YearAll(Y) {
        var Ystr = "";
        for (var y = Y - 10; y <= Y + 10; y++) {
            Ystr += "<li onclick='getym(this,\"cy\")'>" + y + "</li>";
        }
        return Ystr;
    }

    function DateAll() {
        var Mstr = "";
        for (var m = 1; m <= 12; m++) {
            Mstr += "<li onclick='getym(this,\"cm\")'>" + (m < 10 ? "0" + m : m) + "</li>";
        }
        return Mstr;
    }

    function getym(o, s) {
        document.getElementById(s).innerHTML = parseInt(o.innerHTML);
        getDynamicTable(parseInt(cy.innerHTML), parseInt(cm.innerHTML));
    }

    function Month(s) {
        var y = parseInt(cy.innerHTML), m = parseInt(cm.innerHTML);
        if (s == "l") {
            if (m <= 1) {
                m = 12;
                y--;
            } else {
                m--;
            }
        } else {
            if (m >= 12) {
                m = 1;
                y++;
            } else {
                m++;
            }
        }
        cy.innerHTML = y;
        cm.innerHTML = m;
        getDynamicTable(y, m);
    }

    function now() {
        getDynamicTable(SY, SM);
        cy.innerHTML = SY;
        cm.innerHTML = SM;
    }

    function getDynamicTable(Y, M) {
        var Temp, i, j;
        FirstDate = GetWeekdayMonthStartsOn(Y, M);// '得到该月的第一天是星期几  0-6
        MonthDate = GetDaysInMonth(Y, M);// '得到该月的总天数 30
        var lm, ly;
        if (M <= 1) {
            lm = 12;
            ly = Y - 1;
        } else {
            lm = M - 1;
        }
        LastMonthDate = GetDaysInMonth(ly, lm); //上个月的总天数 31
        ErtNum = FirstDate + MonthDate;// -1 
        Temp = "";
        CirNum = 42;
        //alert(Y+","+M);
        //alert("第一天:"+FirstDate+"; 总天数:"+MonthDate+"; ErtNum:"+ErtNum+"; 表格行数:"+(CirNum/7));
        j = 1;
        for (i = 1; i <= CirNum; i++) {
            if (i == 1) {
                Temp += "<table><tr><th>日</th><th>一</th><th>二</th><th>三</th><th>四</th><th>五</th><th>六</th></tr><tr>";
            }
            if (i < FirstDate + 1) {
                Temp += "<td class='past'>" + (LastMonthDate - FirstDate + i) + "</td>";
            } else if (i > ErtNum) {
                Temp += "<td class='coming'>" + (i - ErtNum) + "</td>";
            } else {
//				Temp += (SY == Y && SM == M && SD == j ? "<td class='now'>" : "<td>") + j + "</td>";
                if (SY == Y && SM == M && SD == j) {
                    Temp += "<td class='now'>"+ j +"</td>";
                } else {
                    Temp += "<td>" + j + "</td>";
                }
                j = j + 1
            }
            if (i % 7 == 0) {
                Temp += "</tr><tr>";
            }
            if (i == CirNum) {
                Temp += "</tr></table>";
            }
        }
        document.getElementById("DayAll").innerHTML = Temp;
    }

    function GetDaysInMonth(Y, M) {				//'得到该月的总天数
        if (M == 1 || M == 3 || M == 5 || M == 7 || M == 8 || M == 10 || M == 12)
            return 31;
        else if (M == 4 || M == 6 || M == 9 || M == 11)
            return 30;
        else if (M == 2)
            if ((Y % 4 == 0 && Y % 100 != 0) || (Y % 100 == 0 && Y % 400 == 0))
                return 29;
            else
                return 28;
        else
            return 28;
    }

    function GetWeekdayMonthStartsOn(Y, M) {		//'得到该月的第一天是星期几
        var date = new Date(Y, M - 1, 1);     // java script中月份是0-11
        return date.getDay();
    }
</script>
