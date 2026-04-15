@extends('admin.layouts.app')

@section('panel')

<!-- BEGIN: Content-->


<!-- Wishlist Starts -->
<a class="btn btn-sm btn--primary text-white" href="{{ route('admin.withdraw.method.create') }}"><i class="fa fa-fw fa-plus"></i>@lang('Add New')</a>
<br><br>
<section id="wishlist" class="grid-view wishlist-items">
   @forelse($methods as $method)
  <div class="card ecommerce-card">
    <div class="item-img text-center">
      <a href="#">
        <img src="{{ getImage(imagePath()['withdraw']['method']['path'].'/'. $method->image,imagePath()['withdraw']['method']['size'])}}" class="img-fluid" alt="img-placeholder" />
      </a>
    </div>
    <div class="card-body">
      <div class="item-wrapper">
        <div class="item-rating">

        </div>
        <div class="item-cost">
          <h6 class="item-price">
           @if($method->status == 1)
            <badge class="bg-success badge font-weight-normal badge-success">@lang('Active')</badge>
           @else
            <badge class="bg-danger badge font-weight-normal badge-warning">@lang('Disabled')</badge>
           @endif
          </h6>
        </div>
      </div>
      <div class="item-name">
        <a href="#">{{__($method->name)}} </a>
      </div>

    </div>
    <div class="item-options text-center">
     @if($method->status == 0)
     <form action="{{ route('admin.withdraw.method.activate') }}" method="POST">
                    @csrf
                    <input value="{{$method->id}}" type="hidden" name="id">
      <button type="submit"  class="btn btn-success btn-wishlist remove-wishlist">
        <i data-feather="check"></i>
        <span>Activate</span>
      </button>
    </form>
    @else
     <form action="{{ route('admin.withdraw.method.deactivate') }}" method="POST">
                    @csrf
                    <input value="{{$method->id}}" type="hidden" name="id">
     <button type="submit"  class="btn btn-danger btn-block remove-wishlist">
        <i data-feather="x"></i>
        <span>Deactivate</span>
      </button>
    </form>
    @endif


      <a href="{{ route('admin.withdraw.method.edit', $method->id)}}" class="btn btn-primary btn-cart">
        <i data-feather="edit"></i>
        <span class="add-to-cart">Edit</span>
      </a>
    </div>
  </div>
  @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
  @endforelse
</section>
<!-- Wishlist Ends -->



@endsection

