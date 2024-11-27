<template>
  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped table-md">
          <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Id</th>
            <th style="width: 30em">Secret</th>
            <th>Redirect</th>
            <th>Last Updated</th>
            <th></th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(client, index) in matter" :key="`client-row-` + index">
            <td>{{ index + 1 }}</td>
            <td>{{ client.name }}</td>
            <td>{{ client.id }}</td>
            <td>
              <div data-toggle="tooltip" data-placement="top" title="click to reveal!" @click="revealSecret(index)">
                <code v-if="client.hasOwnProperty('revealed') && client.revealed">{{ client.secret }}</code>
                <code v-else>sealed!</code>
              </div>
            </td>
            <td>{{ client.redirect }}</td>
            <td>{{ dateFormat(client.updated_at) }}</td>
            <td>
              <a :href="'/back-office/client/' + client.id + '/edit'" class="mr-4"><i class="fas fa-edit alert-warning"></i></a>
              <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="top"
                      @click="deleteClient(client.id)"
                      title="delete client!"><i class="fas fa-trash alert-danger"></i>
              </button>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "PassportList",
  data: () => ({
    matter: null
  }),
  methods: {
    load() {
      this.$axios.get('/oauth/clients').then(res => this.matter = res.data)
    },
    revealSecret(index) {
      this.matter[index].revealed = true
      this.$forceUpdate()
    },
    deleteClient(clientId) {
      this.swal({
        title: 'Are you sure to delete this client ?',
        text: 'Sync may failed in instance that use this client credentials !',
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      }).then((result) => {
        if (result) {
          this.$axios.delete('/oauth/clients/' + clientId).then(() => this.reload())
        }
      })
    }
  },
  mounted() {
    this.load()
  }
}
</script>

<style scoped>

</style>
