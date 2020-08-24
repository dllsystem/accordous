export default {
  joinAddress(address) {
    if (!address) return
    var fullAddress = [];
    fullAddress.push(address.address_street)
    fullAddress.push(address.address_number)
    fullAddress.push(address.address_city)
    fullAddress.push(address.address_state)
    // remover items nulos para evitar virgulas duplas
    fullAddress = fullAddress.filter( el => el != null )
    return fullAddress.join(', ')
  }
} 