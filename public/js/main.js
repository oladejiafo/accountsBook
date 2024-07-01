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

    var searchInputs = document.querySelectorAll(".input-group.search .textinput");
    searchInputs.forEach(function(input) {
        var clearBtn = createClearButton();
        input.parentNode.appendChild(clearBtn);

        // Position the clear button
        positionClearButton(input, clearBtn);

        // Show/hide the clear button based on input content
        input.addEventListener("input", function() {
            toggleClearButton(input, clearBtn);
            positionClearButton(input, clearBtn);
        });

        // Clear input content when clear button is clicked
        clearBtn.addEventListener("click", function() {
            input.value = "";
            toggleClearButton(input, clearBtn);
            positionClearButton(input, clearBtn);
        });
    });

    function toggleClearButton(input, clearBtn) {
        clearBtn.classList.toggle("d-none", input.value === "");
    }

    function positionClearButton(input, clearBtn) {
        var inputRect = input.getBoundingClientRect();
        var inputRight = inputRect.right;
        var inputLeft = inputRect.left;

        // Adjust position if the clear button exceeds input width
        if (clearBtn.offsetLeft + clearBtn.offsetWidth > inputRight) {
            clearBtn.style.left = inputLeft - 90 + "px"; // Move leftwards by 90px
        } else {
            clearBtn.style.left = ""; // Reset position
        }
    }

    // Select all tables and add the resizable-table class
    document.querySelectorAll("table.table").forEach(function (table) {
        table.classList.add("resizable-table");
    });

    // Initialize colResizable on tables with the resizable-table class
    if (typeof $ !== "undefined" && $.fn.colResizable) {
        $(".table").colResizable({
            liveDrag: true,
            postbackSafe: true,
            gripInnerHtml: "<div class='grip'></div>",
            draggingClass: "dragging",
        });
    }

    /////////////////////////////////////////////////////////////////////////////////
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

    // Event listener for row grip mousedown
    document.querySelectorAll(".table tbody tr").forEach(function(row) {
        let grip = document.createElement("div");
        grip.className = "row-grip";
        row.appendChild(grip);

        grip.addEventListener("mousedown", function(event) {
            startY = event.clientY;
            currentRow = row;
            startHeight = currentRow.offsetHeight;
            currentRow.classList.add("dragging-row");
            document.addEventListener("mousemove", onMouseMove);
            document.addEventListener("mouseup", onMouseUp);
        });
    });
       
 
    


});
