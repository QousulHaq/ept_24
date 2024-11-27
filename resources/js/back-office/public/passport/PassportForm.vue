<template>
  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-md-7 col-sm-12 form-group row">
          <label class="col-md-3 col-form-label">Name</label>
          <div class="col-md-9">
            <input id="create-client-name" type="text" class="form-control" v-model="form.name">
            <span class="form-text text-muted">Something your users will recognize and trust.</span>
          </div>
        </div>
        <div class="col-md-7 col-sm-12 form-group row">
          <label class="col-md-3 col-form-label">Redirect URL</label>
          <div class="col-md-9">
            <input type="text" class="form-control" name="redirect" v-model="form.redirect">
            <span class="form-text text-muted">Your application's authorization callback URL.</span>
          </div>
        </div>
        <div class="col-md-7 col-sm-12">
          <div class="form-group">
            <button :class="{'btn': true, 'btn-success': true, 'btn-block': true, 'disabled': loading}" @click="save">Save</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import _ from 'lodash'

export default {
  name: "PassportCreate",
  props: {
    client: {
      type: Object,
      nullable: true,
    }
  },
  data: () => ({
    loading: false,
    form: {
      name: '',
      redirect: '',
      confidential: true,
    }
  }),
  computed: {
    mode: function () {
      return this.client ? 'update' : 'create'
    }
  },
  methods: {
    save: _.debounce(function() {
      this.loading = true
      if (this.mode === 'create') {
        this.$axios.post('/oauth/clients', this.form)
          .then(() => this.redirect('/back-office/client'))
      } else if (this.mode === 'update') {
        this.$axios.put('/oauth/clients/' + this.client.id, this.form)
          .then(() => this.reload())
      }
    })
  },
  created() {
    if (this.client) {
      this.form.name = this.client.name
      this.form.redirect = this.client.redirect
      this.mode = 'update'
    }
  }
}
</script>

<style scoped>

</style>
