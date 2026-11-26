(function () {
    "use strict";

    // Toast flash otomatis menghilang
    document.querySelectorAll("[data-autohide]").forEach(function (el) {
        setTimeout(function () {
            el.classList.add("toast-hilang");
            setTimeout(function () {
                el.remove();
            }, 400);
        }, 4500);
    });

    // Konfirmasi sebelum aksi destruktif
    document.querySelectorAll("form[data-confirm]").forEach(function (form) {
        form.addEventListener("submit", function (e) {
            if (!window.confirm(form.getAttribute("data-confirm")))
                e.preventDefault();
        });
    });

    // Modal bukti transfer (native <dialog>)
    document.querySelectorAll("[data-modal-open]").forEach(function (btn) {
        btn.addEventListener("click", function () {
            var dialog = document.getElementById(
                btn.getAttribute("data-modal-open"),
            );
            if (dialog && typeof dialog.showModal === "function")
                dialog.showModal();
        });
    });
    document
        .querySelectorAll("dialog.modal [data-tutup]")
        .forEach(function (btn) {
            btn.addEventListener("click", function () {
                var dialog = btn.closest("dialog");
                if (dialog) dialog.close();
            });
        });

    document.querySelectorAll("[data-print-nota]").forEach(function (btn) {
        btn.addEventListener("click", function () {
            var nota = btn.closest(".nota-transaksi");
            if (!nota) return;
            nota.setAttribute("data-printing", "true");
            window.print();
        });
    });
    window.addEventListener("afterprint", function () {
        document
            .querySelectorAll(".nota-transaksi[data-printing]")
            .forEach(function (nota) {
                nota.removeAttribute("data-printing");
            });
    });

    // Foto unggahan dipotong ke rasio 16:9 sebelum dikirim.
    document.querySelectorAll("[data-image-crop]").forEach(function (input) {
        var field = input.closest(".image-upload");
        var form = input.closest("form[data-image-crop-form]");
        if (!form) return;
        var editor = field.querySelector("[data-crop-editor]");
        var frame = field.querySelector("[data-crop-frame]");
        var preview = field.querySelector("[data-crop-preview]");
        var zoom = field.querySelector("[data-crop-zoom]");
        var horizontal = field.querySelector("[data-crop-horizontal]");
        var vertical = field.querySelector("[data-crop-vertical]");
        var zoomValue = field.querySelector("[data-crop-zoom-value]");
        var status = field.querySelector("[data-crop-status]");
        var error = field.querySelector("[data-crop-error]");
        var currentImage = field.querySelector(".image-upload-current");
        var objectUrl = null;

        function updatePreview() {
            if (!preview.naturalWidth || !frame.clientWidth) return;

            var scale =
                Math.max(
                    frame.clientWidth / preview.naturalWidth,
                    frame.clientHeight / preview.naturalHeight,
                ) * Number(zoom.value);
            var width = preview.naturalWidth * scale;
            var height = preview.naturalHeight * scale;
            var overflowX = Math.max(0, width - frame.clientWidth);
            var overflowY = Math.max(0, height - frame.clientHeight);

            preview.style.width = width + "px";
            preview.style.height = height + "px";
            preview.style.left =
                (-overflowX * Number(horizontal.value)) / 100 + "px";
            preview.style.top =
                (-overflowY * Number(vertical.value)) / 100 + "px";
            zoomValue.textContent = Math.round(Number(zoom.value) * 100) + "%";
        }

        [zoom, horizontal, vertical].forEach(function (control) {
            control.addEventListener("input", updatePreview);
        });

        input.addEventListener("change", function () {
            var file = input.files && input.files[0];
            if (!file) return;

            if (objectUrl) URL.revokeObjectURL(objectUrl);
            objectUrl = URL.createObjectURL(file);
            form.dataset.cropReady = "";
            error.hidden = true;
            error.textContent = "";
            status.textContent =
                "Sesuaikan area foto menggunakan pengaturan di bawah.";
            zoom.value = "1";
            horizontal.value = "50";
            vertical.value = "50";
            editor.hidden = false;
            if (currentImage) currentImage.hidden = true;
            preview.onload = updatePreview;
            preview.onerror = function () {
                error.textContent =
                    "Foto tidak dapat dibuka. Silakan pilih berkas gambar lain.";
                error.hidden = false;
            };
            preview.src = objectUrl;
        });

        form.addEventListener("submit", function (event) {
            if (form.dataset.cropReady === "true") {
                delete form.dataset.cropReady;
                return;
            }

            var inputs = Array.prototype.filter.call(
                form.querySelectorAll("[data-image-crop]"),
                function (candidate) {
                    return candidate.files && candidate.files.length;
                },
            );
            if (!inputs.length) return;
            event.preventDefault();

            if (form.dataset.cropProcessing === "true") return;
            form.dataset.cropProcessing = "true";
            Promise.all(inputs.map(cropFile))
                .then(function (files) {
                    files.forEach(function (file, index) {
                        var transfer = new DataTransfer();
                        transfer.items.add(file);
                        inputs[index].files = transfer.files;
                    });
                    form.dataset.cropProcessing = "";
                    form.dataset.cropReady = "true";
                    var submitter = event.submitter;
                    if (submitter) form.requestSubmit(submitter);
                    else form.requestSubmit();
                })
                .catch(function (cause) {
                    form.dataset.cropProcessing = "";
                    var target = inputs[0]
                        .closest(".image-upload")
                        .querySelector("[data-crop-error]");
                    target.textContent =
                        cause.message ||
                        "Foto gagal diproses. Silakan coba lagi.";
                    target.hidden = false;
                });
        });

        function cropFile(cropInput) {
            var cropField = cropInput.closest(".image-upload");
            var image = cropField.querySelector("[data-crop-preview]");
            var cropZoom = Number(
                cropField.querySelector("[data-crop-zoom]").value,
            );
            var cropX =
                Number(
                    cropField.querySelector("[data-crop-horizontal]").value,
                ) / 100;
            var cropY =
                Number(cropField.querySelector("[data-crop-vertical]").value) /
                100;
            var original = cropInput.files[0];

            if (!image.naturalWidth || !image.naturalHeight) {
                return Promise.reject(
                    new Error(
                        "Foto belum siap diproses. Silakan pilih ulang fotonya.",
                    ),
                );
            }
            if (typeof DataTransfer === "undefined") {
                return Promise.reject(
                    new Error(
                        "Browser tidak mendukung pemotongan foto. Gunakan browser versi terbaru.",
                    ),
                );
            }

            var aspect = 16 / 9;
            var baseWidth = Math.min(
                image.naturalWidth,
                image.naturalHeight * aspect,
            );
            var baseHeight = baseWidth / aspect;
            var cropWidth = baseWidth / cropZoom;
            var cropHeight = baseHeight / cropZoom;
            var sourceX = (image.naturalWidth - cropWidth) * cropX;
            var sourceY = (image.naturalHeight - cropHeight) * cropY;
            var canvas = document.createElement("canvas");
            canvas.width = 1280;
            canvas.height = 720;

            var context = canvas.getContext("2d");
            if (!context)
                return Promise.reject(
                    new Error("Foto gagal diproses. Silakan coba lagi."),
                );
            context.imageSmoothingQuality = "high";
            context.drawImage(
                image,
                sourceX,
                sourceY,
                cropWidth,
                cropHeight,
                0,
                0,
                canvas.width,
                canvas.height,
            );

            return new Promise(function (resolve, reject) {
                canvas.toBlob(
                    function (blob) {
                        if (!blob) {
                            reject(
                                new Error(
                                    "Foto gagal diproses. Silakan coba lagi.",
                                ),
                            );
                            return;
                        }
                        var filename =
                            original.name.replace(/\.[^.]+$/, "") || "foto";
                        resolve(
                            new File([blob], filename + "-16x9.jpg", {
                                type: "image/jpeg",
                            }),
                        );
                    },
                    "image/jpeg",
                    0.82,
                );
            });
        }
    });

    // Tab (ruang belajar / detail kelas)
    document.querySelectorAll("[data-tabs]").forEach(function (grup) {
        var buttons = Array.prototype.slice.call(
            grup.querySelectorAll("[data-tab]"),
        );
        var panels = Array.prototype.slice.call(
            grup.querySelectorAll("[data-tab-panel]"),
        );

        function pilihTab(btn, simpanUrl) {
            var target = btn.getAttribute("data-tab");
            buttons.forEach(function (button) {
                button.classList.toggle("aktif", button === btn);
            });
            panels.forEach(function (panel) {
                panel.hidden = panel.getAttribute("data-tab-panel") !== target;
            });

            if (simpanUrl && window.history && window.history.replaceState) {
                var url = new URL(window.location.href);
                url.searchParams.set("tab", target);
                window.history.replaceState(
                    null,
                    "",
                    url.pathname + url.search + url.hash,
                );
            }
        }

        var tabAwal = new URLSearchParams(window.location.search).get("tab");
        var tombolAwal = buttons.find(function (btn) {
            return btn.getAttribute("data-tab") === tabAwal;
        });
        if (tombolAwal) pilihTab(tombolAwal, false);

        buttons.forEach(function (btn) {
            btn.addEventListener("click", function () {
                pilihTab(btn, true);
            });
        });
    });
})();
