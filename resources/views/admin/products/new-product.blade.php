@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {!! !is_null($product) ? 'Update Product <span class="product-header-title">'.$product->title."</span>" : 'New Product' !!}
                </div>

                <div class="card-body">
                    <form action="{{ route('new-product')}}" class="row" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if (!is_null($product))
                        <input type="hidden" name="_method" value="put">
                        <input type="hidden" name="product_id" value="{{$product->id}}">
                        @endif

                        <div class="form-group col-md-6">
                            <label for="product_title">Product titel</label>
                            <input type="text" name="product_title" class="form-control" id="product_title" placeholder="product title" value="{{ !is_null($product) ? $product->title : ''}}" required>

                        </div>

                        <div class="form-group col-md-12">
                            <label for="product_description">Product description</label>
                            <textarea rows="10" name="product_description" class="form-control" id="product_description" placeholder="product description" required>
                            {{ !is_null($product) ? $product->description : ''}}
                            </textarea>

                        </div>

                        <div class="form-group col-md-12">
                            <label for="product_category">Product category</label>
                            <select name="product_category" id="product_category" class="form-control" required>
                                <option>Select a category</option>
                                @foreach ($categories as $category)
                                <option value="{{$category->id}}" {{ (!is_null($product) && ($product->category->id === $category->id)) ? 'selected' : ''}}>{{$category->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="product_unit">Product unit</label>
                            <select name="product_unit" id="product_unit" class="form-control" required>
                                <option>Select a unit</option>
                                @foreach ($units as $unit)
                                <option value="{{$unit->id}}" {{ (!is_null($product) && ($product->has_unit->id === $unit->id)) ? 'selected' : ''}}>{{$unit->formatted()}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="product_discount">Product Discount</label>
                            <input type="number" class="form-control" id="product_discount" name="product_discount" placeholder="Product_discount" value="{{ (!is_null($product)) ? $product->discount : ''}}">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="product_price">Product price</label>
                            <input type="number" class="form-control" id="product_price" name="product_price" placeholder="Product_price" value="{{ (!is_null($product)) ? $product->price : ''}}">
                        </div>

                        <div class="form-group col-md-12">
                            <label for="product_price">Product Total</label>
                            <input type="number" class="form-control" id="product_total" name="product_total" placeholder="Product_total" value="{{ (!is_null($product)) ? $product->total : ''}}">
                        </div>

                        {{-- Options --}}
                        <div class="form-group col-md-12">
                            <table id="option-table" class="table table-striped">

                            </table>
                            <a href="#" class="btn btn-primary add-option-btn">Add Option</a>

                        </div>

                        {{-- //Options --}}

                        {{-- Images --}}
                        <div class="form-group col-md-12">
                            <div class="row">
                                @for($i = 0 ; $i < 6 ; $i++) <div class="col-md-4 col-sm-12 mb-4">
                                    <div class="card image-card-upload">
                                        <a href="#" class="remove-image-upload">
                                            <i class="fas fa-minus-circle"></i>
                                        </a>
                                        <a href="#" class="activate-image-upload" data-fileid="image-{{$i}}">
                                            <div class="card-body" style>
                                                <i class="fas fa-image"></i>
                                            </div>
                                        </a>
                                        <input name="product_images[]" type="file" class="form-control-file image-file-upload" id="image-{{$i}}">
                                    </div>
                            </div>
                            @endfor
                        </div>
                </div>
            </div>

            {{-- //Images --}}



            <div class="form-group col-md-12">
                <button type="submit" class="btn btn-primary">Save New product</button>
            </div>

        </div>
        </form>
    </div>
</div>
</div>
</div>

<div class="modal option-window" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Option</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body row">
                <p id="edit-message"></p>

                <div class="form-group col-md-6">
                    <label for="option_name">Option name</label>
                    <input type="text" name="option_name" class="form-control" id="option_name" placeholder="option name" required>

                </div>

                <div class="form-group col-md-6">
                    <label for="option_value">Option value</label>
                    <input type="text" name="option_value" class="form-control" id="option_value" placeholder="option value" required>

                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary add-option-button">Add Option</button>
            </div>

        </div>
    </div>
</div>
</div>


@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        var optionNameList = [];
        var optionNamesRow = "";

        var $optionWindow = $('.option-window');
        var $addOptionBtn = $('.add-option-btn');
        var $optionTable = $('#option-table');

        var $activateImageUpload = $('.activate-image-upload');

        $addOptionBtn.on('click', function(e) {
            e.preventDefault();
            $optionWindow.modal('show');
        })

        $(document).on('click', '.remove-option', function(e) {
            e.preventDefault();
            $(this).parent().parent().remove();
        });

        $(document).on('click', '.add-option-button', function(e) {
            e.preventDefault();

            var $optionName = $('#option_name');
            var optionName = $optionName.val();
            if (optionName === '') {
                alert('Option name is Required');
                return false;
            }

            var $optionValue = $('#option_value');
            var optionValue = $('#option_value').val();
            if (optionValue === '') {
                alert('Option value is Required');
                return false;
            }

            if (!optionNameList.includes($optionName.val())) {
                optionNameList.push($optionName.val());

                optionNamesRow =
                    '<td>' +
                    '<input type="hidden" name="options[]" value="' +
                    $optionName.val() +
                    '">'
                '</td>';
            }

            var optionRow = '<tr>' +
                '<td>' +
                optionName +
                '</td>' +
                '<td>' +
                optionValue +
                '</td>' +
                '<td>' +
                '<a href="" class="remove-option"><i class="fas fa-minus-circle"></i></a>' +
                '<input type="hidden" name="' + $optionName.val() + '[]" value="' + $optionValue.val() + '"' +
                '</td>' +
                '</tr>';
            $optionTable.append(optionRow);
            $optionTable.append(optionNamesRow);
            $optionValue.val('');



        });

        function readURL(input, imageID) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#' + imageID).attr('src', e.target.result);
                    // console.log(e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function resetFileUpload(fileUploadID, imageID ,$el , $ed){
            $('#'+imageID).attr('src' , '');
            $el.fadeIn();
            $ed.fadeOut();
            $('#' + fileUploadID).val('');
        }

        $activateImageUpload.on('click', function(e) {
            e.preventDefault();
            var fileUploadID = $(this).data('fileid');
            var me = $(this);
            $('#' + fileUploadID).trigger('click');
            var imagetag = '<img id="i' + fileUploadID + '" scr =" " class="card-img-top">';
            //   var imagetag = '<img id="i' + fileUploadID + '" scr =" " class="card-img-top">';
            $(this).append(imagetag);
            $('#' + fileUploadID).on('change', function(e) {
                readURL(this, 'i' + fileUploadID);
                me.find('i').fadeOut();
                var $removeThisImage = me.parent().find('.remove-image-upload');
                $removeThisImage.fadeIn();

                $removeThisImage.on('click', function(e) {
                    e.preventDefault();
                    resetFileUpload(fileUploadID, 'i' + fileUploadID,  me.find('i'), $removeThisImage);
                });

            });
        });
    });
</script>

@endsection