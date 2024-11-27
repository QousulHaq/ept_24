import swal from 'sweetalert'
import Vue from 'vue'

class SwalPlugin extends Plugin {
	static install () {
		Vue.swal = swal;
		window.swal = swal;
		Object.defineProperties(Vue.prototype, {
			swal: {
				get () {
					return swal
				}
			}
		})
	}
}

Vue.use(SwalPlugin);
