const params = new URLSearchParams(window.location.search);

const name = params.get("name");
const img = params.get("img");
const role = params.get("role");

document.getElementById("docName").innerText = name;
document.getElementById("docRole").innerText = role;
document.getElementById("docImg").src = img;

// Slot selection
let selectedSlot = "";

document.querySelectorAll(".slot-btn").forEach(btn => {
    btn.addEventListener("click", () => {
        document.querySelectorAll(".slot-btn").forEach(b => b.classList.remove("active-slot"));
        btn.classList.add("active-slot");
        selectedSlot = btn.innerText;
    });
});

// Confirm slot
document.querySelector(".confirm-btn").addEventListener("click", () => {
    if (!selectedSlot) {
        alert("Please select a slot first!");
        return;
    }

    alert(`Appointment booked with ${name} at ${selectedSlot}`);
});
