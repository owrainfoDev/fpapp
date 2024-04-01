var tempSave;
$(function () {
    var $btn = $('#tempSaveBtn');
    var SessionData = '';
    if ($btn.length > 0) {
        tempSave = {
            init: async function () {
                SessionData = this.data();
                if (SessionData == "") return;
                let name = SessionData.name;
                let loadData = '';
                loadData = await this.get(name);
                if (loadData == null) return;

                let j = JSON.parse(loadData.TEMP_VALUE);
                let now = new Date();
                let currentTime = now.getTime();

                if (currentTime > j.expireTime) {
                    this.delete();
                    return;
                }

                let value = j.value;
                this.loadData(value);
            },

            set: function (name, value) {
                let now = new Date();
                let item = {
                    value: value,
                    expireTime: now.getTime() + 7200000,
                };
                let data = {
                    TEMP_KEY: name,
                    TEMP_VALUE: item,
                }
                fetch("/api/ajax/tempSave_save", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify(data)
                    })
                    .then((response) => response.json())
                    .then((data) => {
                        console.log('임시저장 성공');
                    });
            },
            get: async function (name) {
                let data = {
                    TEMP_KEY: name
                }
                const response = await fetch("/api/ajax/tempSave_get", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(data)
                })
                const result = await response.json();
                // console.log(result);
                return result;

            },
            data: function () {
                let j = $btn.data('target-area');
                return j;
            },
            loadData: function (data) {
                Swal.fire({
                    text: "임시저장 내용을 불러옵니다.",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "확인",
                    cancelButtonText: "취소"
                }).then((result) => {
                    if (result.isConfirmed) {
                        let json = JSON.parse(data);
                        $.each(json, function (i, k) {
                            $('#' + i).val(k);
                        })
                    }
                })
            },
            delete: function () {
                let data = {
                    TEMP_KEY: SessionData.name,
                }
                fetch("/api/ajax/tempSave_remove", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify(data)
                    })
                    .then((response) => response.json())
                    .then((data) => {
                        console.log(data);
                    });

            }

        };
        tempSave.init();

        $btn.on('click', function () {
            Swal.fire({
                text: "해당내용을 임시 저장 하시겠습니까?",
                // icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#00341E",
                cancelButtonColor: "#dddd",
                confirmButtonText: "확인",
                cancelButtonText: "취소"
            }).then((result) => {
                if (result.isConfirmed) {
                    var data = tempSave.data();
                    let name = data.name;
                    let value = {};
                    $.each(data.target, function (i, v) {
                        value[v] = $('#' + v).val();
                    });
                    tempSave.set(name, JSON.stringify(value));
                }
            });
        })
    }


})