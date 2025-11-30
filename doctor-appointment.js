const params = new URLSearchParams(window.location.search);

document.getElementById("docName").innerText = params.get("name") || "";
document.getElementById("docRole").innerText = params.get("role") || "";
document.getElementById("docImg").src = params.get("img") || "";

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
    if (btn) btn.classList.add("active-slot");

    selectedSlot = time;
}



// -----------------------------------------------------
// CONFIRM SLOT -> Open Login Modal
// -----------------------------------------------------

const confirmBtn = document.getElementById("confirmSlotBtn");
if (confirmBtn) {
    confirmBtn.addEventListener("click", () => {
        if (!selectedDate) {
            alert("Select a date");
            return;
        }
        if (!selectedSlot) {
            alert("Select a time slot");
            return;
        }

        document.getElementById("slotModal").style.display = "none";
        document.getElementById("loginModal").style.display = "block";
    });
} else {
    console.warn("confirmSlotBtn not found in DOM");
}


// -----------------------------------------------------
// LOGIN BUTTON -> Sends Data to PHP
// -----------------------------------------------------

document.getElementById("loginBtn").addEventListener("click", function () {
    let user = document.getElementById("loginUser").value.trim();
    let pass = document.getElementById("loginPass").value.trim();

    if (user === "" || pass === "") {
        alert("Please enter both username and password");
        return;
    }

    // Debug: show spinner or disable button if you want (not included)
    fetch("login.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `username=${encodeURIComponent(user)}&password=${encodeURIComponent(pass)}`
    })
    .then(res => res.text())
    .then(text => {
        console.log("Raw login.php response:", JSON.stringify(text));
        const resp = text.trim();

        if (resp === "success") {
            // close login modal (optional)
            document.getElementById("loginModal").style.display = "none";

            // redirect to your PHP confirmation page (will require PHP server)
            window.location.href =
                `appointment-confirm.php?date=${encodeURIComponent(selectedDate)}&slot=${encodeURIComponent(selectedSlot)}`;
        } else {
            // show server response for debugging
            alert("Invalid login");
        }
    })
    .catch(err => {
        console.error("Login fetch error:", err);
        alert("Error contacting server. Make sure you're running a PHP server and login.php exists.");
    });
});



// -----------------------------------------------------
// MODAL CLOSE BUTTON
// -----------------------------------------------------

const slotClose = document.querySelector(".close");
if (slotClose) slotClose.onclick = () => {
    document.getElementById("slotModal").style.display = "none";
};

const loginClose = document.querySelector(".login-close");
if (loginClose) loginClose.onclick = () => {
    document.getElementById("loginModal").style.display = "none";
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

    const elMonth = document.getElementById("cdpMonth");
    if (elMonth) elMonth.innerText = `${monthNames[month]} ${year}`;

    const daysDiv = document.getElementById("cdpDays");
    if (!daysDiv) return;
    daysDiv.innerHTML = "";

    let firstDay = new Date(year, month, 1).getDay();
    let totalDays = new Date(year, month + 1, 0).getDate();

    for (let i = 0; i < firstDay; i++) {
        daysDiv.innerHTML += `<div class="cdp-empty"></div>`;
    }

    let today = new Date();

    for (let day = 1; day <= totalDays; day++) {
        let classes = "cdp-day";

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
    selectedDate = `${year}-${String(month + 1).padStart(2,"0")}-${String(day).padStart(2,"0")}`;

    document.querySelectorAll(".cdp-day").forEach(d => d.classList.remove("selected"));
    if (event && event.target) event.target.classList.add("selected");

    document.getElementById("slotModal").style.display = "block";
    loadSlots();
}



// -----------------------------------------------------
// MONTH SWITCHING
// -----------------------------------------------------

const prevBtn = document.getElementById("cdpPrev");
if (prevBtn) prevBtn.onclick = () => {
    cdpMonth--;
    if (cdpMonth < 0) { cdpMonth = 11; cdpYear--; }
    loadCircularCalendar(cdpMonth, cdpYear);
};

const nextBtn = document.getElementById("cdpNext");
if (nextBtn) nextBtn.onclick = () => {
    cdpMonth++;
    if (cdpMonth > 11) { cdpMonth = 0; cdpYear++; }
    loadCircularCalendar(cdpMonth, cdpYear);
};


// INITIAL LOAD
loadCircularCalendar(cdpMonth, cdpYear);
