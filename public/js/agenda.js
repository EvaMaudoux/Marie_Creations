import { Calendar } from '@fullcalendar/core';

let Agenda = (function () {

    function initialize() {

        window.onload = () => {
            let calendarElt = document.querySelector("#calendrier_admin")
            let calendar = new Calendar(calendarElt, {
                initialView: 'timeGridWeek',
                locale: 'fr',
                timeZone: 'Europe/Paris',
                headerToolbar: {
                    start: 'prev,next today',
                    center: 'title',
                    end: 'dayGridMonth,timeGridWeek'
                },
            });
            calendar.render()
        }
    }
    return {
        init: initialize
    };
})();
