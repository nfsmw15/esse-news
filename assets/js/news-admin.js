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
    const configEl = document.getElementById('news-editor-config');
    const config = configEl ? JSON.parse(configEl.textContent || '{}') : {};

    $(config.selector).summernote({
        lang: "de-DE",
        height: 380,
        placeholder: "News-Text ...",
        toolbar: [
            ["style",  ["style"]],
            ["font",   ["bold", "italic", "underline", "strikethrough", "clear"]],
            ["color",  ["color"]],
            ["para",   ["ul", "ol", "paragraph"]],
            ["table",  ["table"]],
            ["insert", ["link", "picture", "media", "hr"]],
            ["view",   ["fullscreen", "codeview"]]
        ],
        buttons: {
            media: window.EsseMediaButton,
        },
        callbacks: {
            onImageUpload: function(files) {
                const fd = new FormData();
                fd.append("file", files[0]);
                fetch(config.uploadUrl, { method: "POST", body: fd })
                    .then(r => r.json())
                    .then(d => {
                        if (d.url) $(config.selector).summernote("insertImage", d.url, files[0].name);
                        else alert(d.error || "Upload fehlgeschlagen.");
                    });
            }
        }
    });
})();
