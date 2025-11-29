const doctors = {
    Neurology: [
        { name: "Dr. Jessica Brown", role: "Neurology Specialist", img: "img/doc1.jpg" },
        { name: "Dr. Norosin White", role: "Neuro Surgeon", img: "img/doc2.jpg" }
    ],
    Surgery: [
        { name: "Dr. Brain Adam", role: "General Surgeon", img: "img/doc3.jpg" },
        { name: "Dr. Rain Adam", role: "Heart Surgeon", img: "img/doc4.jpg" }
    ],
    Pathology: [
        { name: "Dr. Daniel Brown", role: "Pathology Expert", img: "img/doc5.jpg" },
        { name: "Dr. Fabricn Brown", role: "Lab Specialist", img: "img/doc6.jpg" }
    ],
    Orthopedics: [
        { name: "Dr. Horesin Kafur", role: "Bone Specialist", img: "img/doc7.jpg" },
        { name: "Dr. Labrian Brown", role: "Orthopedic Doctor", img: "img/doc8.jpg" }
    ]
};

// get category from URL
const params = new URLSearchParams(window.location.search);
const category = params.get("category");

document.getElementById("pageTitle").innerText = `${category} Specialists`;

const list = document.getElementById("doctorList");

// load doctors
doctors[category].forEach(doc => {
    list.innerHTML += `
        <div class="doctor-card">
            <img src="${doc.img}" alt="${doc.name}">
            <h4>${doc.name}</h4>
            <p>${doc.role}</p>
            <button class="book-btn">Visit Profile</button>
            <button class="book-btn" onclick="bookDoctor('${doc.name}', '${doc.img}', '${doc.role}')">Book Now</button>

        </div>
    `;
});

function bookDoctor(name, img, role) {
    window.location.href = 
        `doctor-appointment.html?name=${encodeURIComponent(name)}&img=${encodeURIComponent(img)}&role=${encodeURIComponent(role)}`;
}

