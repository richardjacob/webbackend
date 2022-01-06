@extends('template')
  


@section('main')
<div class="container" style="margin-top:50px;">
  <div class="col-md-7">
    <h2 class="text-center" style="margin-top:50px;margin-bottom:20px;">যোগাযোগ করুন</h2>
    <form action="{{ url('contact_us') }}" method="post">
      {!! csrf_field() !!}
      <div class="form-group">
        <label for="email">আপনার নাম *</label>
        <input type="text" class="form-control" id="name" placeholder="Your Name" name="name" required>
      </div>

      <div class="form-group">
        <label for="pwd">ইমেইল এড্রেস *</label>
        <input type="email" class="form-control" id="email" placeholder="Email" name="email" required>
      </div>

      <div class="form-group">
        <label for="pwd">মোবাইল নাম্বার *</label>
        <input type="text" class="form-control" id="mobile" placeholder="Mobile No." name="mobile" required>
      </div>

      <div class="form-group">
        <label for="email">যোগাযোগের ধরন *</label>
        <select id="contact_for" name="contact_for" placeholder="Type of contact" class="form-control" required>
          <option value="">Select</option>
          <option value="Passenger">যাত্রী (Passenger)</option>
          <option value="Driver">গাড়ি-চালক (Driver)</option>
          <option value="Partner">অংশীদার (Partner)</option>
          <option value="Complain">অভিযোগ (Complain)</option>
          <option value="Other">অন্যান্য (Other)</option>
        </select>
      </div>

      <div class="form-group">
        <label for="pwd">মেসেজ *</label>
        <textarea class="form-control" rows="5" id="msg" placeholder="Message" name="msg" required></textarea>
        <div>আমরা আপনার মন্তব্য চাই: প্রশ্ন, ত্রুটি প্রতিবেদন, অভিযোগ, প্রশংসা, বৈশিষ্ট্য, অনুরোধ  ইত্যাদি। আলেশা  রাইডকে উন্নত করতে আমরা কী করতে পারি তা আমাদের জানান।</div>
      </div>
      
      <button type="submit" class="btn btn-primary">যোগাযোগ করুন</button>
    </form>
  </div>
  <div class="col-md-1">&nbsp;</div>
  <div class="col-md-4 text-left"><br> <br><br><br> <br><br>
    হেল্পলাইন নাম্বার: +৮৮ ০৯৬০৬১১০৫৫০<br> <br><br>

    ঠিকানা <br><br>
    আলেশা রাইড লিমিটেড <br> <br>

    নাসরিন টাওয়ার ৫ম তলা <br> <br>

    ৩৫১ বীর উত্তম মীর শওকত সড়ক ,  <br> <br>

    লিফট-৫, ২৪১ তেজগাঁও ঢাকা ১২০৮ <br> <br>
  </div>
</div>




@stop

