document.addEventListener('DOMContentLoaded', function() {
    const calendarContent = document.querySelector('.calendar-content');

    // Generate the yearly calendar dynamically
    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    const dayNames = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    
    const festivalsAndHolidays = [
        { month: 0, day: 1, name: "New Year's Day" },
        { month: 0, day: 14, name: "Makar Sankranti" },
        { month: 0, day: 26, name: "Republic Day" },
        { month: 1, day: 14, name: "Valentine's Day" },
        { month: 2, day: 8, name: "International Women's Day" },
        { month: 2, day: 21, name: "Holi" },
        { month: 3, day: 1, name: "April Fool's Day" },
        { month: 3, day: 14, name: "Baisakhi" },
        { month: 4, day: 1, name: "May Day" },
        { month: 4, day: 9, name: "Mother's Day" },
        { month: 5, day: 5, name: "World Environment Day" },
        { month: 5, day: 21, name: "Father's Day" },
        { month: 6, day: 1, name: "Canada Day" },
        { month: 6, day: 4, name: "Independence Day (USA)" },
        { month: 7, day: 15, name: "Indian Independence Day" },
        { month: 7, day: 22, name: "Eid al-Adha" },
        { month: 8, day: 5, name: "Teachers' Day" },
        { month: 8, day: 10, name: "Ganesh Chaturthi" },
        { month: 9, day: 2, name: "Gandhi Jayanti" },
        { month: 9, day: 14, name: "Diwali" },
        { month: 9, day: 25, name: "Christmas" },
        { month: 6, day: 7 , name: "MSD Birthday"}
    ];

    monthNames.forEach((month, monthIndex) => {
        const monthContainer = document.createElement('div');
        monthContainer.classList.add('month-container');
        const monthHeader = document.createElement('div');
        monthHeader.classList.add('month-header');
        const monthName = document.createElement('div');
        monthName.classList.add('month-name');
        monthName.textContent = month;
        monthHeader.appendChild(monthName);
        monthContainer.appendChild(monthHeader);

        const monthBody = document.createElement('div');
        monthBody.classList.add('month-body');

        // Add the day names header
        dayNames.forEach(dayName => {
            const dayNameContainer = document.createElement('div');
            dayNameContainer.classList.add('day-name');
            dayNameContainer.textContent = dayName;
            monthBody.appendChild(dayNameContainer);
        });

        const daysInMonth = new Date(2024, monthIndex + 1, 0).getDate();
        const firstDay = new Date(2024, monthIndex, 1).getDay();

        // Add empty slots for days before the first day of the month
        for (let i = 0; i < firstDay; i++) {
            const emptyDay = document.createElement('div');
            emptyDay.classList.add('day');
            monthBody.appendChild(emptyDay);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const dayContainer = document.createElement('div');
            dayContainer.classList.add('day');
            dayContainer.textContent = day;

            const date = new Date(2024, monthIndex, day);
            if (date.getDay() === 0 || date.getDay() === 6) {
                dayContainer.classList.add('weekend');
            } else {
                dayContainer.classList.add('weekday');
            }

            const festival = festivalsAndHolidays.find(f => f.month === monthIndex && f.day === day);
            if (festival) {
                dayContainer.classList.add('festival');
                dayContainer.setAttribute('data-tooltip', festival.name);

                // Add event listener for the holiday alert
                dayContainer.addEventListener('click', () => {
                    alert(`Holiday: ${festival.name}`);
                });
            }

            monthBody.appendChild(dayContainer);
        }

        monthContainer.appendChild(monthBody);
        calendarContent.appendChild(monthContainer);
    });

    // Scroll functionality
    const scrollLeft = document.querySelector('.scroll-left');
    const scrollRight = document.querySelector('.scroll-right');
    scrollLeft.addEventListener('click', () => {
        calendarContent.scrollBy({
            left: -calendarContent.clientWidth,
            behavior: 'smooth'
        });
    });
    scrollRight.addEventListener('click', () => {
        calendarContent.scrollBy({
            left: calendarContent.clientWidth,
            behavior: 'smooth'
        });
    });
});
