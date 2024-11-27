<template>
  <button type="button" class="btn btn-sm mr-4" @click="deleteItem"><i class="fas fa-trash alert-danger"></i></button>
</template>

<script>
  export default {
    name: "DeleteExistingItem",
    props: {
      package: {
        required: true,
        type: String
      },
      item: {
        required: true,
        type: String
      }
    },
    methods: {
      deleteItem () {
        this.swal("Are you sure you want to do this ?", {
          buttons: ["Cancel", "Yes, continue!"],
          dangerMode: true
        }).then(value => {
          if (value) {
            this.request('api.back-office.package.item.destroy', {
              package__: this.package,
              item: this.item
            }).then((res) => {
              if (res.data.status === 'success') {
                location.reload()
              }
            })
          }
        })
      }
    }
  }
</script>

<style scoped>

</style>
