$.fn.tooltip = function(opt) {
    return this.each(function() {
        if (typeof opt === "string") {
            const t = bootstrap.Tooltip.getInstance(this);
            if (t) t[opt]();
        } else {
            new bootstrap.Tooltip(this, opt || {});
        }
    });
};
$.fn.popover = function(opt) {
    return this.each(function() {
        if (typeof opt === "string") {
            const p = bootstrap.Popover.getInstance(this);
            if (p) p[opt]();
        } else {
            new bootstrap.Popover(this, opt || {});
        }
    });
};

(function() {
    $("#news").summernote({
        lang: "de-DE",
        height: 380,
        placeholder: "News-Text ...",
        toolbar: [
            ["style",  ["style"]],
            ["font",   ["bold", "italic", "underline", "strikethrough", "clear"]],
            ["color",  ["color"]],
            ["para",   ["ul", "ol", "paragraph"]],
            ["table",  ["table"]],
            ["insert", ["link", "picture", "hr"]],
            ["view",   ["fullscreen", "codeview"]]
        ],
        callbacks: {
            onImageUpload: function(files) {
                const fd = new FormData();
                fd.append("file", files[0]);
                fetch("/admin/files/upload", { method: "POST", body: fd })
                    .then(r => r.json())
                    .then(d => {
                        if (d.url) $("#news").summernote("insertImage", d.url, files[0].name);
                        else alert(d.error || "Upload fehlgeschlagen.");
                    });
            }
        }
    });
})();
