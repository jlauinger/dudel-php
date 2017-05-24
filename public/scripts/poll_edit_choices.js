var times = [];
var dates = [];
var calendar;

$(document).ready(function() {
    calendar = $("#calendar");

    // Checkbox stuff
    $(".checkbox-cell :checkbox").on("click", updateCheckbox).on("click", function(e) {
        e.stopPropagation();
    });

    $(".checkbox-cell").on("click", function(e){
        var checkbox = $(this).find(":checkbox");
        checkbox.prop("checked", !checkbox.prop("checked"));
        updateCheckbox.call(checkbox);
    });

    $(".checkbox-cell :checkbox").each(updateCheckbox);

    // Checkbox all/none toggles
    $(".toggle").click(function() {
        // deselect or select
        var selected = $(this).hasClass("toggle-select");
        var cells;
        if($(this).hasClass("toggle-column")) {
            var index = $(this).closest("td").index() + 1;
            cells = $(this).closest("table").find("tr td:nth-child(" + index + ")");
        } else if($(this).hasClass("toggle-row")) {
            cells = $(this).closest("tr").find("td");
        } else {
            cells = $(this).closest("table").find("td");
        }
        updateCheckbox.call(cells.find(":checkbox").prop("checked", selected));
        return false;
    });

    // Calendar stuff
    $(".calendar").each(function() {
        initCalendar($(this));
    });

    // Time slider
    initTimeSlider();

    // Time remove buttons
    $(".time-slots").on("click", ".time-remove-button", function() {
        removeTime($(this).attr("data-time"));
        return false;
    });

    $("#calendar-list").on("click", ".date-remove-button", function() {
        removeDate($(this).attr("data-date"));
        return false;
    });

    $("#date-time-form-content").hide();

    // Load data
    if($("#times").val()) times = $("#times").val().split(",");
    if($("#dates").val()) dates = $("#dates").val().split(",");
    times.sort();
    dates.sort();
    updateDateTimeList();

});

/* ========================================================================== */
/* Checkbox stuff */

function updateCheckbox(e) {
    $(this).closest(".checkbox-cell").removeClass("on off").addClass($(this).prop("checked") ? "on" : "off");
}

/* ========================================================================== */
/* Time and date stuff */

function updateDateTimeList() {
    $("#times").val(times.join());
    $("#dates").val(dates.join());

    $(".time-slots").html("");
    times.forEach(function(time) {
        $(".time-slots").append('<li><button class="action time-remove-button" title="remove time" data-time="' + time + '"><span>' + time + '</span><i class="fa fa-times"></i></button></li>');
    });

    $("#calendar-list").html("");
    dates.forEach(function(date) {
        var formatDate = moment(date).format("ddd D MMM");
        $("#calendar-list").append('<li><button class="action date-remove-button" title="remove date" data-date="' + date +'"><span>' + formatDate + '</span><i class="fa fa-times"></i></button></li> ');
    });

    if($('#calendar-list').length) {
        calendarSetDate(calendar.data("date"));
    }
}

function addTime(rawTime) {
    var time = moment(rawTime, "HH:mm", true);
    if(time.isValid()) {
        times.push(time.format("HH:mm"));
        times = times.uniquify();
        times.sort();
        updateDateTimeList();
    }
}

function addDate(date) {
    dates.push(date);
    dates = dates.uniquify();
    dates.sort();
    updateDateTimeList();
}

function removeTime(time) {
    times.removeValue(time);
    updateDateTimeList();
}

function removeDate(date) {
    dates.removeValue(date);
    updateDateTimeList();
}


/* ========================================================================== */
/* Time Slider */

function initTimeSlider() {
    setSliderPosition(12, 0);

    $("#time-add-button").click(function() {
        var hour = ("00"+$("#time-hour").val()).substr(-2);
        var minute = ("00"+$("#time-minute").val()).substr(-2);
        addTime(hour + ":" + minute);
        $("#time-hour").focus();
        return false;
    });

    $('.suggested-time').click(function() {
        addTime($(this).data('time'));
    });

    $("#time-minute, #time-hour").focus(function(e) {
        $(this).select();
    });
}

function setSliderPosition(_hour, _minute, suppressValueUpdate) {
    var hour = (_hour + (_minute-(_minute%60))/60) % 24;
    var minute = _minute % 60;

    if(!suppressValueUpdate || hour!=_hour || minute!=_minute) {
        $("#time-hour").val(hour);
        $("#time-minute").val(minute < 10 ? "0" + minute : minute);
    }
}


/* ========================================================================== */
/* Calendar */

function initCalendar(calendar) {
    var date = moment();
    calendarSetDate(date);

    $("#calendar-next-month").click(function() {
        calendarSetDate(calendar.data("date").add("months", 1));
        return false;
    });

    $("#calendar-prev-month").click(function() {
        calendarSetDate(calendar.data("date").subtract("months", 1));
        return false;
    });

    calendar.on("click", "button.day", function() {
        toggleDay($(this));
        return false;
    }).on("click", "button.week", function() {
        var off = $(this).closest("tr").find("button.day.default").length > 0;
        var cls = off ? ".default" : ".success";

        $(this).closest("tr").find("button.day" + cls).each(function() {
            toggleDay($(this));
        });
        return false;
    });
}

function toggleDay(btn) {
    var date = calendar.data("date");
    date.date(btn.text());
    var datetime = date.format("YYYY-MM-DD");
    if(dates.contains(datetime)) {
        removeDate(datetime);
    } else {
        addDate(datetime);
    }
}

function calendarSetDate(date) {
    date = date.startOf("month"); // first day of the month
    calendar.data("date", date);
    updateMonth();
}

function updateMonth() {
    var date = calendar.data("date");

    calendar.find(".title").text(date.format("MMMM YYYY"));
    calendar.find(".week").remove();

    var week = [];
    var start = date.isoWeekday();
    var endDay = date.endOf("month").date();
    var end = Math.ceil( (endDay + start) / 7 ) * 7;
    for(var i = 1; i <= end; ++i) {
        var day = i - start;
        week.push(day >= 0 && day < endDay ? day+1 : 0);
        if(i%7 == 0) {
            makeWeek(week);
            week = [];
        }
    }
}

function makeWeek(week) {
    var tr = $('<tr class="week"></tr>');

    var end = moment(calendar.data("date"));
    end.date(week[6]);
    var start = moment(calendar.data("date"));
    start.date(week[0]);
    var past = end.isBefore(moment());
    var future = start.isAfter(moment());
    if(past && !future) {
        tr.append($('<td class="left"></td>'))
    } else {
        tr.append($('<td class="left"><button class="week action icon default"><i class="fa fa-angle-double-right"></i></button></td>'))
    }

    for(var i = 0; i < 7; ++i) {
        var btn = "";
        if(week[i] != 0) {
            var date = calendar.data("date");
            date.date(week[i]);
            var datetime = date.format("YYYY-MM-DD");
            // alert(datetime);
            var enabled = dates.contains(datetime);
            var past = date.isBefore(moment());
            var t = (past && !enabled ? 'span' : 'button');
            btn = '<' + t + ' class="day ' + (enabled ? 'action icon success' : (past ? '' : 'action icon default')) + '">' + week[i] + '</' + t + '>';
        }
        tr.append($('<td>' + btn + '</td>'));
    }
    calendar.find("table").append(tr);
}