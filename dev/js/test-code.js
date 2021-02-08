$('body').on('click', '.password-control', function(){
	if ($('#password-input').attr('type') == 'password'){
		$(this).addClass('view');
		$('#password-input').attr('type', 'text');
	} else {
		$(this).removeClass('view');
		$('#password-input').attr('type', 'password');
	}
	return false;
});



t._v(" "), t.isClearHelperVisible ? r("div", {
				staticClass: "auth-input__helper auth-input__helper_clear auth-input__helper_visible auth-form__input-helper",
				on: {
					click: t.onClearField
				}
			})



t.isEyeHelperVisible ? r("div", {
				staticClass: "auth-input__helper auth-input__helper_visible auth-form__input-helper",
				class: [t.isPasswordVisible ? "auth-input__helper_insecure" : "auth-input__helper_secure"],
				on: {
					click: t.togglePasswordVisible
				}
			})


 t._e(), t._v(" "), t.isDisableHelperVisible ? r("div", {
				staticClass: "auth-input__helper auth-input__helper_hint auth-input__helper_visible auth-form__input-helper"
			}, [r("div", {
				staticClass: "auth-form__hint auth-form__hint_primary auth-form__hint_tiny auth-form__hint_extended"
			}, [r("a", {
				staticClass: "auth-form__link auth-form__link_primary auth-form__link_tiny",
				on: {
					click: function click(e) {
						e.preventDefault();
						return t.onEnable(e)
					}
				}
			}, [t._v("\n                                РР·РјРµРЅРёС‚СЊ\n                            ")])])]) 