// Read doctor details
const params = new URLSearchParams(window.location.search);

document.getElementById("docName").innerText = params.get("name");
document.getElementById("docRole").innerText = params.get("role");
document.getElementById("docImg").src = params.get("img");

let selectedSlot = "";
let selectedDate = "";

// Slot templates
const morningSlots = [
    "09:00 AM", "10:00 AM", "11:00 AM", "12:00 PM",
    "01:00 PM", "02:00 PM", "03:00 PM"
];

const eveningSlots = [
    "05:00 PM", "06:00 PM", "07:00 PM", "08:00 PM"
];

document.getElementById("appointmentDate").addEventListener("change", function () {
    selectedDate = this.value;

    if (!selectedDate) return;

    document.getElementById("slotSection").classList.remove("hidden");

    loadSlots();
});

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

    alert(`Your appointment is confirmed on ${selectedDate} at ${selectedSlot}`);
});
