@extends('layouts.admin')

@section('content')
	<div class="box-body pad">
		<div class="box">
			<div class="box-body" id="searchwords">
                {!! Form::open(['route' => 'searchwords.save']) !!}
                    <div class="form-group row heading-row">
                        <div class="col-md-3"><b>Сокращение</b></div>
                        <div class="col-md-9">
                            <b>Расшифровка</b>
                            <a href="#" class="btn btn-success btn-sm pull-right" @click="addRow"><i class="fa fa-plus"></i></a>
                        </div>
                    </div>


                    <div class="form-group row" v-for="(word, index) in words">
                        <div class="col-md-3">
                            <input type="text" :name="'words[' + index + '][short]'" v-model="word.short" class="form-control">
                        </div>

                        <div class="col-md-9">
                            <input type="text" :name="'words[' + index + '][long]'" v-model="word.long" class="form-control with-btn">

                            <a href="#" class="btn btn-danger btn-sm remove-btn" @click="removeRow(index)"><i class="fa fa-minus"></i></a>
                        </div>
                    </div>


					<p>
                        <br>
						<a href="{{ route('admin.index') }}" class="btn btn-danger">Отмена</a>
						<input type="submit" class="btn btn-success" value="Сохранить">
					</p>
			    {!! Form::close() !!}
			</div>
		</div>
	</div>
@endsection



@section('scripts')
    <script src="/assets/admin_assets/vue.min.js"></script>
    <script>
        let searchwords = new Vue({
            el: '#searchwords',

            data: {
                words: {!! $searchwords !!},
            },

            methods: {
                addRow: function() {
                    this.words.push({short: '', long: ''});
                },

                removeRow: function(index) {
                    this.words.splice(index, 1);
                }
            }
        });
    </script>

    <style>
        #searchwords .form-group {
            margin-bottom: 3px;
        }

        #searchwords .row.heading-row {
            margin-bottom: 10px;
        }

        #searchwords .form-group.row {
            margin-left: 0;
            margin-right: 0;
        }

        #searchwords .form-group .col-md-3,
        #searchwords .form-group .col-md-9 {
            padding: 0;
        }

        #searchwords .form-group .col-md-3 {
            padding-right: 3px;
        }

        #searchwords .form-control.with-btn {
            width: calc(100% - 34px);
            float: left;
        }

        #searchwords .remove-btn {
            margin-top: 2px;
            float: right;
        }
    </style>
@endsection