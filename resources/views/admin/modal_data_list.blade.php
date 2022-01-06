<table class="table table-bordered">
  <thead>
      <tr>
          <th>SL#</th>
          <th>Trip ID</th>
          <th>Driver ID</th>
          <th>Rider ID</th>
          <th>Pickup Location</th>
          <th>Drop Location</th>
          <th>Total Fare</th>
          <th>Owe Amount</th>
          <th>RemainingOwe Amount</th>
          <th>Start Time</th>
          <th>End Time</th>
      </tr>
  </thead>
  <tbody>                    
    @foreach($list as $sl => $data)
    <tr>
      <td>{{$sl+1}}</td>
      <td>{{$data->id}}</td>
      <td>{{$data->driver_id}}</td>
      <td>{{$data->user_id}}</td>
      <td>{{$data->pickup_location}}</td>
      <td>{{$data->drop_location}}</td>
      <td>{{$data->total_fare}}</td>
      <td>{{$data->owe_amount}}</td>
      <td>{{$data->remaining_owe_amount}}</td>
      <td>{{$data->begin_trip}}</td>
      <td>{{$data->end_trip}}</td>
    </tr>
    @endforeach
</tbody>
</table>