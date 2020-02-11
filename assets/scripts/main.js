import FormFields from "./modules/FormFields";

class Form {
    static init() {
        FormFields.init();
    }
}

jQuery(document).ready(function () {
   Form.init();
});
