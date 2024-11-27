<template>
  <div>
    <div class="form-group">
      <label for="name">Name</label>
      <select id="name" class="form-control" v-model="chosenClientId">
        <option v-for="(client, i) in clients" :key="`option-`+i" :value="client.id">{{client.name}}</option>
      </select>
    </div>
    <div class="form-group">
      <label for="passphrase">Passphrase</label>
      <input type="password" id="passphrase" class="form-control" v-model="passphrase">
    </div>
    <button type="button" class="btn btn-secondary" data-dismiss="modal" >Close</button>
    <button type="submit" class="btn btn-primary" @click="save">Save changes</button>
  </div>
</template>

<script>
export default {
  name: "AssignPackageToClient",
  props: {
    packageId: {
      type: String,
      required: true,
    }
  },
  data: () => ({
    clients: [],
    chosenClientId: null,
    passphrase: null,
  }),
  methods: {
    save() {
      if (this.chosenClientId) {
        this.request('api.back-office.package.share', {
          'package__': this.packageId,
          'client': this.chosenClientId,
          'passphrase': this.passphrase,
        }).then(() => this.reload())
      }
    }
  },
  mounted() {
    this.$axios.get('/oauth/clients').then(({ data: responseData }) => this.clients = responseData)
  }
}
</script>

<style scoped>

</style>
