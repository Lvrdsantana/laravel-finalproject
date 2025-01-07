const modal = document.getElementById("courseModal");
const addCourseBtn = document.getElementById("addCourseBtn");
const closeBtn = document.getElementsByClassName("close")[0];
const courseForm = document.getElementById("courseForm");
const classTimetable = document.getElementById("classTimetable");
const classSelect = document.getElementById("classSelect");

let currentClassId = null;

classSelect.addEventListener("change", function() {
    currentClassId = this.value;
    // Fetch and display timetable for selected class
});

addCourseBtn.addEventListener("click", function() {
    modal.style.display = "block";
});

closeBtn.addEventListener("click", function() {
    modal.style.display = "none";
});

window.onclick = function(event) {
    if (event.target === modal) {
        modal.style.display = "none";
    }
};

courseForm.addEventListener("submit", function(e) {
    e.preventDefault();
    const formData = new FormData(courseForm);
    formData.append('class_id', currentClassId);

    fetch(courseForm.action, {
        method: 'POST',
        body: formData
    }).then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update timetable display
            modal.style.display = "none";
        }
    });
});

document.getElementById('classSelect').addEventListener('change', function() {
    document.getElementById('class_id').value = this.value;
});