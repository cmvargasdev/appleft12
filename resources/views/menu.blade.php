<h1>Menu</h1>
<table>
@foreach( $categories as $cat)

    <tr><th colspan="2">ID:{{$cat->id}} - {{$cat->name}}</th></tr>

    @foreach($products->where('product_category_id',$cat->id) as $product)
    <tr>
        <td>POS:{{$product->pos}}</td>
        <td>{{$product->name}}</td>
        <td>
             @if($product->has_variants)
                <b>Var:</b>
                @foreach ($product->variants as $variant)
                    {{ $variant->name }}:{{ $variant->price }} /
                @endforeach
            @else
                <b>Uni:</b> {{$product->price}}
            @endif
        </td>
    </tr>
    @endforeach

@endforeach
</table>
