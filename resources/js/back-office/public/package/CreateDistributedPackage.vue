<template>
  <div>
    <div class="form-group">
      <label for="base_url">Base Uri</label>
      <input name="base_url" id="base_url" type="text" class="form-control" v-model="form.base_uri">
    </div>
    <div class="form-group">
      <label for="client_id">Client Id</label>
      <input name="client_id" id="client_id" type="text" class="form-control" v-model="form.client_id">
    </div>
    <div class="form-group">
      <label for="client_secret">Client Secret</label>
      <input name="client_secret" id="client_secret" type="text" class="form-control" v-model="form.client_secret">
    </div>
    <button v-if="packages.length === 0" type="submit" class="btn btn-dark" @click="getAccessiblePackage">Get accessible packages</button>
    <div v-else class="form-group">
      <label for="package_id">Package</label>
      <select name="package_id" id="package_id" class="form-control" v-model="form.package_id">
        <option v-for="(_package, i) in packages" :key="'package-option-'+i" :value="_package.id">{{_package.title}}</option>
      </select>
    </div>
    <button type="submit" class="btn btn-dark" v-if="canSave" @click="save">Save</button>
    <button type="reset" class="btn btn-default">Reset</button>
  </div>
</template>

<script>
export default {
  name: "CreateDistributedPackage",
  data: () => ({
    form: {
      base_uri: null,
      client_id: null,
      client_secret: null,
      package_id: null,
    },
    packages: [],
  }),
  computed: {
    canSave() {
      return Object.values(this.form).every(value => !!value)
    }
  },
  methods: {
    getAccessiblePackage() {
      this.request('api.back-office.package.distributed.shareable', {
        base_uri: this.form.base_uri,
        client_id: this.form.client_id,
        client_secret: this.form.client_secret,
      }).then(({ data: responseData }) => this.packages = responseData.data)
        .then(() => this.packages.length === 0 ? this.swal('Error', 'doesn\'t have any access to packages in the given node', 'error') : null)
    },
    save() {
      this.request('api.back-office.package.distributed.store', this.form).then(() => this.redirect('/back-office/package'))
    }
  },
}
</script>

<style scoped>

</style>
