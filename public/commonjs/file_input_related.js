function generateUniqueNumber() {
    return Date.now().toString() + Math.floor(Math.random() * 10000).toString().padStart(4, '0');
}
function initFilePreviewEvent(container_id = null) {
    function createImagePreview(file, container) {
        let reader = new FileReader();
        reader.onload = function (event) {
            let y = event.target.result;
          
            let wrapper = `
                <div class='image-wrapper'  style='position: relative; display: inline-block; margin: 5px;'>
                    <img src='${y}' class='img_rounded' style='width:100px;height:100px; border: 1px solid #ccc; border-radius: 8px;' />
                    <button type='button' class='delete-btn' style='
                        position: absolute;
                        top: -8px;
                        right: -8px;
                        background: red;
                        color: white;
                        border: none;
                        border-radius: 50%;
                        width: 20px;
                        height: 20px;
                        cursor: pointer;
                        font-size: 14px;
                        line-height: 18px;
                    '>&times;</button>
                </div>
            `;

            $(container).append(wrapper);

            // Attach delete event to latest button
            $(container).find('.delete-btn').last().on('click', function () {
                $(this).parent('.image-wrapper').remove();
            });
        };
        reader.readAsDataURL(file);
    }

    function attachSingleFilePreview(el) {
        $(el).change(function () {
            const file = this.files[0];
            if (file && file.type.startsWith("image/"))  {
                createImagePreview(file, $(el).parent());
            }
        });
    }

    function attachMultipleFilePreview(el) {
        $(el).change(function () {
            const files = this.files;
            if (files.length > 0) {
                
                for (let i = 0; i < files.length; i++) {
                    if (files[i] && files[i].type.startsWith("image/"))  
                    createImagePreview(files[i], $(el).parent());
                }
            }
        });
    }

    if (container_id) {
        $("#" + container_id + " input[type=file]").each(function () {
            let el = this;
            if ($(el).attr("multiple")) {
                attachMultipleFilePreview(el);
            } else {
                attachSingleFilePreview(el);
            }
        });
    } else {
        $("input[type=file]").each(function () {
            let el = this;
            if ($(el).attr("multiple")) {
                attachMultipleFilePreview(el);
            } else {
                attachSingleFilePreview(el);
            }
        });
    }
}
