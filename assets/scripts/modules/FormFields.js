import 'jquery-ui/ui/widgets/sortable';
import Toast from "../../../../CoreBundle/assets/scripts/modules/Toast";

class FormFields {
    static init() {
        this.changePosition();
        this.ajaxContactFormFieldEdit();
    }

    static changePosition() {
        const sort = $("#sortableFormFields");
        sort.sortable({
            update: function (event, ui) {
                let arrayOrderEl = [];
                $('.contact-form-previsualisation-col').each(function (i) {
                    $(this).find('.position').text(i);
                    arrayOrderEl[i] = $(this).data('id');
                });
                $.ajax({
                    method: 'POST',
                    url: '/admin/contact-form/fields/change-position',
                    data: {
                        data: arrayOrderEl
                    },
                    success: function (res) {
                        console.log(res, 'success');
                        if (res === 'valid') {
                            new Toast('Changement de position effectué', 'success', 'Succès', 5000);
                        } else {
                            // TODO : error
                        }
                    },
                    error: function (er) {
                        console.log(er, 'error');
                        new Toast('Une erreur s\'est produite lors du changement de position...', 'danger', 'Une erreur s\'est produite...', 5000);
                    }
                })
            },
        });
    }

    static ajaxPosition() {
    }

    static ajaxContactFormFieldEdit() {
        $('.btn-modal-contactformfield').click(function (e) {
            e.preventDefault();
            const data = $(this).parents('.contact-form-previsualisation-col').data('id');
            const form = $(this).parents('.contact-form-previsualisation-col').data('form');

            fetch('/admin/contact/form/field/' + data + '/edit')
                .then(function (res) {
                    return res.text()
                        .then(function (response) {
                            const modal = $('#modalEditContactFormField');
                            modal.html(response);
                            modal.attr('data-id', data);
                            // $('#modalEditMenuitem > form').attr('name', 'menu_item_ajax');
                        })
                        .then(function () {
                            $('#modalEditContactFormField .btn-update-item').click(function (e) {
                                e.preventDefault();
                                const data = $('#modalEditContactFormField').data('id');

                                $.ajax({
                                    method: 'POST',
                                    url: '/admin/contact/form/field/' + data + '/edit',
                                    data: $('#modalEditContactFormField > form[name=new_contact_form_field]').serialize(),
                                    success: function (res) {
                                        // console.log(res, 'success');
                                        if (res === 'valid') {
                                            window.location.reload();
                                        } else {
                                            // TODO : error
                                        }
                                    },
                                    error: function (er) {
                                        console.log(er, 'error');
                                    }
                                });
                            });
                        });
                })
        });
    }
}

export default FormFields