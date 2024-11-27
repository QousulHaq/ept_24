<template>
  <div>
    <button type="submit" class="btn btn-danger btn-block" @click="save">Detach</button>
  </div>
</template>

<script>
export default {
  name: "DetachPackageToClient",
  props: {
    packageId: {
      type: String,
      required: true,
    },
    clientId: {
      type: Number,
      required: true,
    }
  },
  data: () => ({}),
  methods: {
    save() {
      this.request('api.back-office.package.unshared', {
        'package__': this.packageId,
        'client': this.clientId,
      }).then(() => this.reload())
    }
  },
  mounted() {
    this.$axios.get('/oauth/clients').then(({data: responseData}) => this.clients = responseData)
  }
}
</script>

<style scoped>

</style>
