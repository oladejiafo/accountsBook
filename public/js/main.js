// Search box
document.addEventListener("DOMContentLoaded", function () {
    function createClearButton() {
        var btn = document.createElement("button");
        btn.type = "button";
        btn.className = "btn-clear d-none";
        btn.innerHTML = "&times;";
        btn.setAttribute("aria-label", "Clear search");
        return btn;
    }

    var searchInputs = document.querySelectorAll(
        ".input-group.search .textinput"
    );
    searchInputs.forEach(function (input) {
        var clearBtn = createClearButton();
        input.parentNode.appendChild(clearBtn);

        input.setAttribute("autocomplete", "off");

        function toggleClearButton() {
            clearBtn.classList.toggle("d-none", !input.value);
        }

        // Show the clear button when input has value on focus, input, and keyup events
        input.addEventListener("change", toggleClearButton);
        input.addEventListener("focus", toggleClearButton);
        input.addEventListener("input", toggleClearButton);

        // Clear the input when clear button is clicked
        clearBtn.addEventListener("click", function () {
            input.value = "";
            clearBtn.classList.add("d-none");
            input.focus();
        });

        toggleClearButton();
    });

    // // Select all tables and add the resizable-table class
    // document.querySelectorAll("table.table").forEach(function (table) {
    //     table.classList.add("resizable-table");
    // });

    // // Initialize colResizable on tables with the resizable-table class
    // if (typeof $ !== "undefined" && $.fn.colResizable) {
    //     $(".table").colResizable({
    //         liveDrag: true,
    //         postbackSafe: true,
    //         gripInnerHtml: "<div class='grip'></div>",
    //         draggingClass: "dragging",
    //     });
    // }

// Function to initialize colResizable if on medium and larger screens
function initializeColResizable() {
    if (window.innerWidth >= 768) { // Adjust breakpoint as needed (768px for md)
        document.querySelectorAll("table.table").forEach(function (table) {
            if ($.fn.colResizable) {
                $(table).colResizable({
                    liveDrag: true,
                    postbackSafe: true,
                    gripInnerHtml: "<div class='grip'></div>",
                    draggingClass: "dragging",
                });
            }
        });
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function () {
    initializeColResizable();
});

// Reinitialize on window resize (optional)
window.addEventListener('resize', function () {
    initializeColResizable();
});


///////////////////////////////////////////////////////////////////////////////////

    // Add resizer grips to each table row
    document.querySelectorAll("table.table tbody tr").forEach(function (row) {
        let grip = document.createElement("div");
        grip.className = "row-grip";
        row.style.position = "relative"; 
    });

    // Function to handle row resizing
    let startY, startHeight, currentRow;

    function onMouseMove(event) {
        if (currentRow) {
            const heightDiff = event.clientY - startY;
            currentRow.style.height = `${startHeight + heightDiff}px`;
        }
    }

    function onMouseUp() {
        document.removeEventListener("mousemove", onMouseMove);
        document.removeEventListener("mouseup", onMouseUp);
        if (currentRow) {
            currentRow.classList.remove("dragging-row");
            currentRow = null;
        }
    }

    document.querySelectorAll(".row-grip").forEach(function (grip) {
        grip.addEventListener("mousedown", function (event) {
            startY = event.clientY;
            currentRow = this.parentElement;
            startHeight = currentRow.offsetHeight;
            currentRow.classList.add("dragging-row");
            document.addEventListener("mousemove", onMouseMove);
            document.addEventListener("mouseup", onMouseUp);
        });
    });
});
