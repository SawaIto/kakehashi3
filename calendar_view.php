<?php
session_start();
include("funcs.php");
sschk();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>カレンダー表示</title>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/locales/ja.js"></script>
    <style>
        .event-date {
            background-color: #f0f0f0;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body class="bg-blue-100 min-h-screen flex flex-col">
    <?php include 'header.php'; ?>
    
    <div class="container mx-auto mt-20 p-4">
        <div id="calendar"></div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'listYear',
            locale: 'ja',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'listYear'
            },
            views: {
                listYear: {
                    type: 'list',
                    duration: { years: 1 },
                    buttonText: '年'
                }
            },
            events: 'get_events.php',
            eventClick: function(info) {
                window.location.href = 'event_form.php?id=' + info.event.id;
            },
            <?php if ($_SESSION['kanri_flg'] == 1 || $_SESSION['modify_flg'] == 1): ?>
            dateClick: function(info) {
                window.location.href = 'event_form.php?date=' + info.dateStr;
            },
            <?php endif; ?>
            eventDidMount: function(info) {
                var dateEl = info.el.getElementsByClassName('fc-list-event-time')[0];
                if (dateEl) {
                    var date = info.event.start;
                    var formattedDate = date.getFullYear() + '年' + (date.getMonth() + 1) + '月' + date.getDate() + '日';
                    dateEl.innerHTML = '<div class="event-date">' + formattedDate + '</div>';
                }
            },
            noEventsContent: 'イベントはありません'
        });
        calendar.render();
    });
    </script>
</body>
</html>