<template>
  <div>
    <div class="form-group">
      <label for="query">Search Question</label>
      <input type="text" id="query" class="form-control" v-model="query">
    </div>
    <div>
      <table class="table">
        <thead>
        <tr>
          <th scope="col">Code</th>
          <th scope="col">Category</th>
          <th scope="col"></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(item, index) in payload.data" :key="index">
          <th scope="row">{{item.code}}</th>
          <td>{{item.category_name}}</td>
          <td>
            <button class="btn btn-sm btn-primary" @click="attach(item.id)">pilih</button>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: "AssignQuestionToPackage",
  props: {
    packageId: {
      required: true,
      type: String,
    }
  },
  data: () => ({
    query: null,
    payload: {
      data: []
    },
  }),
  watch: {
    query: function (value) {
      this.loadData(value)
    },
  },
  methods: {
    loadData: _.debounce(function (query = null) {
      this.$axios.get('/api/back-office/item', {
        params: {
          package_id: this.packageId,
          query: query,
          per_page: 5,
        }
      }).then(({ data: responseData}) => {
        this.payload = responseData
      })
    }),
    async attach(itemId) {
      const {data: responseData} = await this.request('api.back-office.item.attach', {
        package__: this.packageId,
        item_id: itemId,
      })

      this.$emit('success')
    },
  },
  mounted() {
    this.loadData()
  }
}
</script>

<style scoped>

</style>
