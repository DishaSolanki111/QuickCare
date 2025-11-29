// -----------------------------------------------------
// LOAD DOCTOR DETAILS FROM URL
// -----------------------------------------------------

const params = new URLSearchParams(window.location.search);

document.getElementById("docName").innerText = params.get("name");
document.getElementById("docRole").innerText = params.get("role");
document.getElementById("docImg").src = params.get("img");

let selectedSlot = "";
let selectedDate = "";


// -----------------------------------------------------
// TIME SLOTS
// -----------------------------------------------------

const morningSlots = [
    "09:00 AM", "10:00 AM", "11:00 AM", "12:00 PM",
    "01:00 PM", "02:00 PM", "03:00 PM"
];

const eveningSlots = [
    "05:00 PM", "06:00 PM", "07:00 PM", "08:00 PM"
];

function loadSlots() {
    const morningDiv = document.getElementById("morningSlots");
    const eveningDiv = document.getElementById("eveningSlots");

    morningDiv.innerHTML = "";
    eveningDiv.innerHTML = "";
    selectedSlot = "";

    morningSlots.forEach(time => {
        morningDiv.innerHTML += `<button class="slot-btn" onclick="selectSlot('${time}')">${time}</button>`;
    });

    eveningSlots.forEach(time => {
        eveningDiv.innerHTML += `<button class="slot-btn" onclick="selectSlot('${time}')">${time}</button>`;
    });
}

function selectSlot(time) {
    document.querySelectorAll(".slot-btn").forEach(b => b.classList.remove("active-slot"));
    const btn = [...document.querySelectorAll(".slot-btn")].find(b => b.innerText === time);
    btn.classList.add("active-slot");

    selectedSlot = time;
}

document.querySelector(".confirm-btn").addEventListener("click", () => {
    if (!selectedDate) {
        alert("Select a date");
        return;
    }
    if (!selectedSlot) {
        alert("Select a time slot");
        return;
    }

    // Close slot modal
    document.getElementById("slotModal").style.display = "none";

    // Show login modal
    document.getElementById("loginModal").style.display = "block";
});

document.querySelector(".login-close").onclick = () => {
    document.getElementById("loginModal").style.display = "none";
};



// -----------------------------------------------------
// MODAL CLOSE BUTTON
// -----------------------------------------------------

document.querySelector(".close").onclick = () => {
    document.getElementById("slotModal").style.display = "none";
};


// -----------------------------------------------------
// MODERN CIRCULAR DATE PICKER
// -----------------------------------------------------

let cdpMonth = new Date().getMonth();
let cdpYear = new Date().getFullYear();

function loadCircularCalendar(month, year) {
    const monthNames = [
        "January","February","March","April","May","June",
        "July","August","September","October","November","December"
    ];

    document.getElementById("cdpMonth").innerText =
        `${monthNames[month]} ${year}`;

    const daysDiv = document.getElementById("cdpDays");
    daysDiv.innerHTML = "";

    let firstDay = new Date(year, month, 1).getDay();
    let totalDays = new Date(year, month + 1, 0).getDate();

    // Empty cells before first day
    for (let i = 0; i < firstDay; i++) {
        daysDiv.innerHTML += `<div class="cdp-empty"></div>`;
    }

    let today = new Date();

    for (let day = 1; day <= totalDays; day++) {
        let classes = "cdp-day";

        // Highlight today
        if (
            day === today.getDate() &&
            month === today.getMonth() &&
            year === today.getFullYear()
        ) {
            classes += " today";
        }

        daysDiv.innerHTML += `
            <div class="${classes}" onclick="selectCircularDate(${day}, ${month}, ${year}, event)">
                ${day}
            </div>
        `;
    }
}

function selectCircularDate(day, month, year, event) {
    selectedDate = `${year}-${month + 1}-${day}`;

    // Remove previous selection
    document.querySelectorAll(".cdp-day").forEach(d => d.classList.remove("selected"));

    // Add selected class
    event.target.classList.add("selected");

    // Open slot modal
    document.getElementById("slotModal").style.display = "block";

    loadSlots();
}


// MONTH SWITCHING
document.getElementById("cdpPrev").onclick = () => {
    cdpMonth--;
    if (cdpMonth < 0) {
        cdpMonth = 11;
        cdpYear--;
    }
    loadCircularCalendar(cdpMonth, cdpYear);
};

document.getElementById("cdpNext").onclick = () => {
    cdpMonth++;
    if (cdpMonth > 11) {
        cdpMonth = 0;
        cdpYear++;
    }
    loadCircularCalendar(cdpMonth, cdpYear);
};


// INITIAL LOAD
loadCircularCalendar(cdpMonth, cdpYear);
