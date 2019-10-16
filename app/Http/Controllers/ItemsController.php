<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemFormValidation;
use App\Item;
use App\ItemSupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ItemsController extends Controller
{
        
    /* 
    *
    *   Store new Item
    *
    */
    public function store(ItemFormValidation $validation)
    {
        $validatedFormData = $validation->validated();

        $pictures = array();

        $item = new Item();

        // If pictures are uploaded
        if (\Request::has('itemPictures')) {
            if ($files = \Request::file('itemPictures')) {
                foreach ($files as $file) {
                    $fileName = $file->getClientOriginalName();
                    $file->move('image', $fileName);
                    $pictures[] = $fileName;
                }
            }
        }

        $item['item_code'] = $validatedFormData['itemCode'];
        $item['item_name'] = $validatedFormData['itemName'];
        $item['item_price'] = $validatedFormData['itemPrice'];
        $item['item_category'] = $validatedFormData['itemCategory'];
        $item['item_description'] = $validatedFormData['itemDescription'];

        if (count($pictures) > 0) {
            $item['item_images'] = implode('|', $pictures);
        }

        $item->save();

        ItemSupplier::firstOrCreate(
            ['item_id' => $item->id],
            [
                'item_id' => $item->id,
                'supplier_name' => \Request::get('supplierName2'),
                'supplier_date' => \Request::get('supplierDate'),
                'supplier_cost' => \Request::get('supplierCost'),
                'supplier_doc' => \Request::hasFile('supplierDoc') ?
                    \Request::file('supplierDoc')->getClientOriginalName() : ''
            ]
        );

        // Store in Storage
        if (\Request::hasFile('supplierDoc')) {
            \Request::file('supplierDoc')->storeAs('public/doc', \Request::file('supplierDoc')->getClientOriginalName());
        }
        // Redirect to homepage
        return redirect('/home')->with('SuccessMessage', 'Item Saved Successfully!');
    }


    /*
    *
    *   Edit Item 
    *
    *   @returns View with Edit Item
    */

    public function edit(Request $request)
    {
        $id = $request->input('editItemId');

        if (!is_numeric($id)) {
            return view('/search')->with('ErrorMessage', 'Items Form is not Valid');
        }
        
        $item = Item::where('id', $id)->first();
        $itemSupplier = ItemSupplier::where('item_id', $id)->get()->all();

        return view('/home', [
            'EditItem' => $item,
            'ItemSupplier' => $itemSupplier,
            'EditItemId' => $id
        ]);
    }


    /*
    *   Update Items Forms
    *
    * 
    */

    public function update(ItemFormValidation $validation)
    {
        $updatedValidatedFormData = $validation->validated();
        $itemToUpdate = Item::find(\Request::input('UpdateItemId'));
        $itemToUpdate['item_name'] = $updatedValidatedFormData['itemName'];
        $itemToUpdate['item_code'] = $updatedValidatedFormData['itemCode'];
        $itemToUpdate['item_price'] = $updatedValidatedFormData['itemPrice'];
        $itemToUpdate['item_description'] = $updatedValidatedFormData['itemDescription'];
        $itemToUpdate['item_category'] = $updatedValidatedFormData['itemCategory'];


        $picsToDelete = request()->input('itemDeletePics');
        if (isset($picsToDelete) && count($picsToDelete) > 0) {
            foreach ($picsToDelete as $pic) {
                if (file_exists('image/' . $pic)) {
                    unlink($pic);
                }
            }
        }

        // If pictures are uploaded
        if (\Request::has('itemPictures')) {
            $allPics = [];
            $pics = [];
            $oldPics = [];
            $oldPicCache = explode('|', Item::where('id', \Request::input('UpdateItemId'))->value('item_images'));

            foreach ($oldPicCache as $oldPic) {
                array_push($oldPics, $oldPic);
            }

            if ($files = \Request::file('itemPictures')) {
                foreach ($files as $file) {
                    $fileName = $file->getClientOriginalName();
                    $file->move('image', $fileName);
                    $pics[] = $fileName;
                }
            }
            
            if (count($pics) > 0) {
                //dd($oldPics);
                $itemToUpdate['item_images'] = implode('|', array_merge($pics, $oldPics));
            }
        }

        $itemToUpdate->save();
        
        return redirect('/home')->with('SuccessMessage', 'Item Updated Successfully!');
    } // end Update

} // end Class
